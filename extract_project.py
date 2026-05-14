import os

def generate_project_context(output_filename="project_context.txt"):
    # 1. Bỏ qua các thư mục build, cache và các nền tảng không cần thiết cho logic
    IGNORE_DIRS = {
        '.git', '.dart_tool', 'build', '.idea', '.vscode', 
        'windows', 'linux', 'macos', 'web', 
        'node_modules', '__pycache__'
    }
    
    # 2. CHỈ ĐỌC nội dung các tệp có đuôi này (Whitelist)
    ALLOWED_EXTS = {
        '.dart', '.yaml', '.xml', '.json', '.md','.php','.sql','css','js'
    }
    
    # 3. Các tệp cụ thể cần bỏ qua (để tránh rác code và file lock)
    IGNORE_FILES = {
        'pubspec.lock', 'package_config.json'
    }

    tree_content = []
    file_contents = []

    # Phần 1: Tạo cấu trúc cây thư mục (Directory Tree)
    tree_content.append("CẤU TRÚC THƯ MỤC (DIRECTORY TREE):\n")
    tree_content.append("=====================================\n")

    start_path = '.'

    for root, dirs, files in os.walk(start_path):
        # Lọc thư mục: loại bỏ các thư mục rác
        dirs[:] = [d for d in dirs if d not in IGNORE_DIRS]

        # Tính toán mức độ thò thụt dựa trên độ sâu của thư mục
        level = root.replace(start_path, '').count(os.sep)
        indent = ' ' * 4 * level
        folder_name = os.path.basename(root)
        
        # Tránh in ra thư mục gốc dưới dạng '.'
        if folder_name == '.' or folder_name == '':
            folder_name = os.path.basename(os.path.abspath(start_path))
            
        tree_content.append(f"{indent}📁 {folder_name}/\n")

        sub_indent = ' ' * 4 * (level + 1)
        for f in files:
            # Bỏ qua chính tệp đang chạy, tệp kết quả, tệp lock và các tệp sinh tự động của Flutter
            if (f == os.path.basename(__file__) or 
                f == output_filename or 
                f in IGNORE_FILES or 
                f.endswith('.g.dart') or      # File sinh tự động của build_runner / json_serializable
                f.endswith('.freezed.dart')): # File sinh tự động của freezed
                continue
                
            tree_content.append(f"{sub_indent}📄 {f}\n")

            # Phần 2: Đọc nội dung tệp (File Contents)
            # Chỉ đọc nội dung nếu tệp có đuôi nằm trong ALLOWED_EXTS
            _, ext = os.path.splitext(f)
            if ext.lower() in ALLOWED_EXTS:
                file_path = os.path.join(root, f)
                try:
                    with open(file_path, 'r', encoding='utf-8') as file_to_read:
                        content = file_to_read.read()
                        file_contents.append(f"\n\n{'='*50}\n")
                        file_contents.append(f"--- ĐƯỜNG DẪN TỆP (FILE PATH): {file_path} ---\n")
                        file_contents.append(f"{'='*50}\n\n")
                        file_contents.append(content)
                except Exception as e:
                    file_contents.append(f"\n\n--- LỖI (ERROR) KHI ĐỌC TỆP {file_path}: {str(e)} ---\n")

    # Phần 3: Ghi tất cả vào tệp văn bản (Text File)
    with open(output_filename, 'w', encoding='utf-8') as out_file:
        out_file.writelines(tree_content)
        out_file.writelines("\n\nNỘI DUNG CHI TIẾT CÁC TỆP (FILE CONTENTS):\n")
        out_file.writelines(file_contents)

    print(f"[THÀNH CÔNG] Đã trích xuất cấu trúc và nội dung dự án Flutter vào tệp: {output_filename}")

if __name__ == "__main__":
    generate_project_context()