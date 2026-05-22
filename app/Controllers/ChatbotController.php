<?php
/**
 * app/controllers/ChatbotController.php
 * Xử lý yêu cầu tư vấn của Chatbot AI (Gemini hoặc Rule-based)
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use PDO;

class ChatbotController
{
    private Product $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    /**
     * POST /chatbot/message
     * Xử lý tin nhắn người dùng gửi lên
     */
    public function message(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
            exit;
        }

        // Lấy tin nhắn người dùng
        $input = json_decode(file_get_contents('php://input'), true);
        $userMsg = trim($input['message'] ?? '');

        if ($userMsg === '') {
            echo json_encode(['success' => false, 'message' => 'Nội dung tin nhắn trống']);
            exit;
        }

        $settingModel = new \App\Models\Setting();
        if ($settingModel->get('chatbot_enabled', '1') !== '1') {
            echo json_encode(['success' => false, 'message' => 'Hệ thống Chatbot hiện đã tạm tắt']);
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Lấy lịch sử chat trong session
        if (!isset($_SESSION['chatbot_history'])) {
            $_SESSION['chatbot_history'] = [];
        }

        // Lấy danh sách sản phẩm để làm ngữ cảnh tư vấn (giới hạn 45 sản phẩm tiêu biểu)
        $db = getDB();
        $stmt = $db->query("
            (
                SELECT p.id, p.name, p.price, p.grade, p.scale, p.series, p.stock, c.name as category_name, c.type as category_type
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND p.grade IS NOT NULL AND p.grade != ''
                ORDER BY p.created_at DESC, p.stock DESC
                LIMIT 30
            )
            UNION ALL
            (
                SELECT p.id, p.name, p.price, p.grade, p.scale, p.series, p.stock, c.name as category_name, c.type as category_type
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1 AND (p.grade IS NULL OR p.grade = '')
                ORDER BY p.created_at DESC, p.stock DESC
                LIMIT 15
            )
        ");
        $productsContext = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $chatbotAiMode = $settingModel->get('chatbot_ai_mode', '1');
        $customKey = $settingModel->get('chatbot_gemini_key', '');
        $apiKey = !empty($customKey) ? $customKey : ($_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY') ?? '');

        if ($chatbotAiMode === '1' && !empty($apiKey)) {
            // Chế độ Chatbot AI (Gemini API)
            $responseMessage = $this->callGeminiAPI($apiKey, $userMsg, $productsContext);
        } else {
            // Chế độ Rule-based dự phòng thông minh
            $responseMessage = $this->getRuleBasedResponse($userMsg, $productsContext);
        }

        // Lưu lịch sử chat (tối đa 12 tin nhắn để tránh quá tải session)
        $_SESSION['chatbot_history'][] = ['role' => 'user', 'text' => $userMsg];
        $_SESSION['chatbot_history'][] = ['role' => 'model', 'text' => $responseMessage];
        if (count($_SESSION['chatbot_history']) > 12) {
            $_SESSION['chatbot_history'] = array_slice($_SESSION['chatbot_history'], -12);
        }

        echo json_encode([
            'success' => true,
            'response' => $responseMessage
        ]);
        exit;
    }

    /**
     * GET /chatbot/clear
     * Xóa lịch sử trò chuyện
     */
    public function clear(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['chatbot_history']);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => 'Đã xóa lịch sử trò chuyện.']);
        exit;
    }

    /**
     * Gọi API của Gemini 1.5 Flash
     */
    private function callGeminiAPI(string $apiKey, string $userMsg, array $productsContext): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

        // Chuẩn bị system instruction chứa danh mục sản phẩm của shop
        $productsJson = json_encode($productsContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        $systemInstruction = "Bạn là Trợ lý AI Tư vấn Gunpla (Gunpla AI Assistant) của cửa hàng 'Gunpla Shop'.\n"
            . "Nhiệm vụ của bạn là giải đáp thắc mắc, hướng dẫn người dùng lựa chọn mô hình Gundam phù hợp.\n"
            . "Dưới đây là danh sách sản phẩm hiện có tại cửa hàng (ngữ cảnh thực tế):\n"
            . "```json\n" . $productsJson . "\n```\n\n"
            . "QUY TẮC BẮT BUỘC:\n"
            . "1. Khi gợi ý bất cứ sản phẩm nào trong danh sách trên, bạn PHẢI dẫn liên kết (link) đến trang chi tiết của sản phẩm đó sử dụng thẻ HTML <a> với cấu trúc: <a href=\"/gunpla-shop/products/detail/[ID]\">[Tên sản phẩm]</a>. Ví dụ: <a href=\"/gunpla-shop/products/detail/2\">HG Gundam Aerial</a>. Tuyệt đối không tự bịa link khác.\n"
            . "2. Nếu sản phẩm khách hàng hỏi không có trong danh sách trên, hãy phản hồi khéo léo là cửa hàng hiện chưa có hàng nhưng đề xuất sản phẩm tương tự có sẵn và kèm link.\n"
            . "3. Trả lời bằng tiếng Việt thân thiện, chuyên nghiệp, súc tích (dưới 180 từ). Sử dụng định dạng in đậm, danh sách gạch đầu dòng hợp lý để dễ đọc.\n"
            . "4. KHÔNG sử dụng cú pháp markdown dạng [Tên](Link), hãy dùng thẻ HTML <a> để hiển thị liên kết trực tiếp trên trang.";

        // Chuẩn bị nội dung contents (lịch sử trò chuyện + tin nhắn mới)
        $contents = [];
        foreach ($_SESSION['chatbot_history'] as $chat) {
            $contents[] = [
                'role' => $chat['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $chat['text']]]
            ];
        }
        // Thêm tin nhắn hiện tại
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMsg]]
        ];

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 500
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 8); // timeout 8s để không treo request

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            // Lỗi kết nối hoặc API Key sai -> chuyển sang Rule-based dự phòng
            return $this->getRuleBasedResponse($userMsg, $productsContext) . "\n\n*(Lưu ý: Hệ thống đang chạy ở chế độ dự phòng do kết nối AI gián đoạn)*";
        }

        $resData = json_decode($response, true);
        $textResult = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty(trim($textResult))) {
            return $this->getRuleBasedResponse($userMsg, $productsContext);
        }

        return $textResult;
    }

    /**
     * Chế độ Rule-based phân tích từ khóa dự phòng
     */
    private function getRuleBasedResponse(string $userMsg, array $productsContext): string
    {
        $msgLower = mb_strtolower($userMsg, 'UTF-8');
        
        $recommended = [];
        $reply = "";

        if (strpos($msgLower, 'mới chơi') !== false || strpos($msgLower, 'người mới') !== false || strpos($msgLower, 'bắt đầu') !== false) {
            $reply = "Chào bạn! Đối với người mới bắt đầu lắp ráp Gunpla, shop khuyên bạn nên chọn dòng **EG (Entry Grade)** hoặc **SD (Super Deformed)** vì các dòng này dễ lắp, khớp dẻo và không cần dùng kìm cắt chuyên nghiệp. Dưới đây là các mẫu phù hợp tại shop:\n";
            $recommended = $this->findProductsByGradeOrType($productsContext, ['EG', 'SD'], 3);
        } elseif (strpos($msgLower, 'hg') !== false || strpos($msgLower, 'high grade') !== false) {
            $reply = "Chào bạn! Dòng **HG (High Grade)** là dòng phổ biến nhất với tỷ lệ 1/144, mẫu mã cực kỳ đa dạng và độ chi tiết vừa phải. Xem các mẫu HG có sẵn tại shop:\n";
            $recommended = $this->findProductsByGradeOrType($productsContext, ['HG'], 3);
        } elseif (strpos($msgLower, 'rg') !== false || strpos($msgLower, 'real grade') !== false) {
            $reply = "Chào bạn! Dòng **RG (Real Grade)** sở hữu tỷ lệ 1/144 nhỏ gọn nhưng độ chi tiết cơ khí và biên độ chuyển động cực kỳ tinh xảo (tương đương MG/PG). Xem các mẫu RG:\n";
            $recommended = $this->findProductsByGradeOrType($productsContext, ['RG'], 3);
        } elseif (strpos($msgLower, 'mg') !== false || strpos($msgLower, 'master grade') !== false) {
            $reply = "Chào bạn! Dòng **MG (Master Grade)** sở hữu tỷ lệ 1/100 to lớn, có khung xương chuyển động bên trong cực kỳ cơ khí và đẹp mắt. Xem các mẫu MG có sẵn:\n";
            $recommended = $this->findProductsByGradeOrType($productsContext, ['MG', 'MG Ver.Ka'], 3);
        } elseif (strpos($msgLower, 'pg') !== false || strpos($msgLower, 'perfect grade') !== false) {
            $reply = "Chào bạn! Dòng **PG (Perfect Grade)** tỷ lệ 1/60 là đỉnh cao của Gunpla với hàng nghìn mảnh ghép, khung xương kim loại và hệ thống đèn LED cực ngầu. Tham khảo các mẫu PG:\n";
            $recommended = $this->findProductsByGradeOrType($productsContext, ['PG', 'PG Unleashed'], 3);
        } elseif (strpos($msgLower, 'dụng cụ') !== false || strpos($msgLower, 'kìm') !== false || strpos($msgLower, 'kéo') !== false || strpos($msgLower, 'keo') !== false || strpos($msgLower, 'nhíp') !== false) {
            $reply = "Chào bạn! Để lắp ráp mô hình đẹp và không bị sứt mẻ nhựa, bạn cần các dụng cụ cơ bản như kìm cắt part (nipper), nhíp gắp decal và dao gọt ghẻ. Tham khảo dụng cụ tại shop:\n";
            $recommended = $this->findProductsByCategoryType($productsContext, ['tool', 'accessory'], 3);
        } elseif (strpos($msgLower, 'giá rẻ') !== false || strpos($msgLower, 'rẻ') !== false || strpos($msgLower, 'bao nhiêu') !== false) {
            $reply = "Chào bạn! Shop có nhiều mẫu Gunpla giá cả vô cùng phải chăng chỉ từ hơn 100k. Dưới đây là các sản phẩm giá cực tốt đang sẵn hàng:\n";
            // Lấy 3 sản phẩm rẻ nhất
            usort($productsContext, fn($a, $b) => (int)$a['price'] - (int)$b['price']);
            $recommended = array_slice(array_filter($productsContext, fn($p) => (int)$p['stock'] > 0), 0, 3);
        } else {
            $reply = "Chào bạn! Tôi là trợ lý Gunpla AI. Bạn cần tìm kiếm mô hình thuộc dòng nào (SD, EG, HG, RG, MG, PG) hay các loại dụng cụ lắp ráp? Dưới đây là một số sản phẩm nổi bật của shop:\n";
            // Lấy 3 sản phẩm ngẫu nhiên
            shuffle($productsContext);
            $recommended = array_slice($productsContext, 0, 3);
        }

        // Tạo chuỗi danh sách liên kết sản phẩm
        if (!empty($recommended)) {
            foreach ($recommended as $p) {
                $priceStr = number_format((int)$p['price'], 0, ',', '.') . 'đ';
                $reply .= "- <a href=\"/gunpla-shop/products/detail/{$p['id']}\">{$p['name']}</a> ({$priceStr}) - Grade: {$p['grade']}\n";
            }
        } else {
            $reply .= "Hiện tại các sản phẩm thuộc nhóm này đang tạm hết hàng. Bạn có thể chat thêm để tìm dòng sản phẩm khác nhé!";
        }

        return $reply;
    }

    private function findProductsByGradeOrType(array $products, array $grades, int $limit): array
    {
        $filtered = array_filter($products, function($p) use ($grades) {
            return in_array(strtoupper(trim($p['grade'] ?? '')), $grades) && (int)$p['stock'] > 0;
        });
        
        // Nếu không đủ sản phẩm còn hàng, lấy cả sản phẩm hết hàng
        if (count($filtered) < $limit) {
            $allFiltered = array_filter($products, function($p) use ($grades) {
                return in_array(strtoupper(trim($p['grade'] ?? '')), $grades);
            });
            $filtered = array_merge($filtered, array_slice($allFiltered, 0, $limit - count($filtered)));
        }

        return array_slice($filtered, 0, $limit);
    }

    private function findProductsByCategoryType(array $products, array $types, int $limit): array
    {
        $filtered = array_filter($products, function($p) use ($types) {
            $catType = strtolower($p['category_type'] ?? '');
            foreach ($types as $type) {
                if (strpos($catType, $type) !== false) return true;
            }
            $catName = mb_strtolower($p['category_name'] ?? '', 'UTF-8');
            foreach (['dụng cụ', 'kềm', 'bút', 'nhíp', 'dao', 'keo', 'sơn', 'phụ kiện'] as $vietKeyword) {
                if (strpos($catName, $vietKeyword) !== false) return true;
            }
            return false;
        });

        return array_slice($filtered, 0, $limit);
    }
}
