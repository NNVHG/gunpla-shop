-- Đảm bảo bạn đang chạy các lệnh này trong database gunpla_shop
INSERT INTO products (name, slug, price, stock, category_id, scale, grade, series, description, thumbnail, weight_gram, is_active) VALUES
-- 41: Kềm cắt chuyên dụng
('Kềm cắt GodHand SPN-120 Ultimate Nipper', 'godhand-spn-120-ultimate-nipper', 1350000, 20, 41, NULL, NULL, NULL, 'Kềm cắt lưỡi đơn huyền thoại GodHand SPN-120, cho vết cắt ngọt, không để lại ghẻ nhựa (nub mark).', 'images/tools/godhand-spn120.jpg', 150, 1),
('Kềm cắt Tamiya 74123 Sharp Pointed Side Cutter', 'tamiya-74123-sharp-pointed-nipper', 780000, 30, 41, NULL, NULL, NULL, 'Kềm cắt Tamiya mũi nhọn cao cấp, độ bền cao, thích hợp để cắt các part nhỏ và sát runner.', 'images/tools/tamiya-74123.jpg', 180, 1),

-- 42: Dao trổ
('Dao trổ Tamiya Design Knife 74020', 'tamiya-design-knife-74020', 190000, 50, 42, NULL, NULL, NULL, 'Dao trổ kỹ thuật Tamiya, đi kèm 30 lưỡi dao dự phòng, dụng cụ không thể thiếu để gọt ghẻ nhựa.', 'images/tools/tamiya-74020.jpg', 50, 1),

-- 43: Nhíp gắp
('Nhíp gắp Tamiya Angled Tweezers 74003', 'tamiya-angled-tweezers-74003', 250000, 40, 43, NULL, NULL, NULL, 'Nhíp mũi cong Tamiya, độ chụm hoàn hảo để gắp và dán các loại decal nước, decal sticker siêu nhỏ.', 'images/tools/tamiya-74003.jpg', 30, 1),

-- 44: Dụng cụ mài nhám
('Bộ dũa thủy tinh Gunprimer Raser', 'gunprimer-raser-glass-file', 650000, 15, 44, NULL, NULL, NULL, 'Dũa thủy tinh cao cấp Gunprimer Raser, xóa dấu ghẻ nhựa mà không làm xước bề mặt chi tiết.', 'images/tools/gunprimer-raser.jpg', 80, 1),
('Giấy nhám xốp GodHand Kamiyasu (Set 400-1000)', 'godhand-kamiyasu-sponge-set', 180000, 50, 44, NULL, NULL, NULL, 'Set nhám xốp GodHand dày 3mm/5mm, linh hoạt chà nhám trên cả bề mặt cong và phẳng.', 'images/tools/godhand-sponge.jpg', 50, 1),

-- 51: Bút kẻ viền
('Tamiya Panel Line Accent Color (Black)', 'tamiya-panel-line-accent-black', 120000, 100, 51, NULL, NULL, NULL, 'Dung dịch kẻ lằn chìm Tamiya màu đen, có sẵn cọ nhỏ ở nắp, giúp chi tiết mô hình nổi bật hơn.', 'images/tools/tamiya-panel-black.jpg', 100, 1),
('Bút kẻ viền Gundam Marker GM01 Black (Fine)', 'gundam-marker-gm01-black', 650000, 100, 51, NULL, NULL, NULL, 'Bút kẻ viền đầu nhỏ GM01 cơ bản của Mr.Hobby, dễ sử dụng cho người mới chơi.', 'images/tools/marker-gm01.jpg', 30, 1),

-- 52: Bút sơn
('Bút sơn Gundam Marker EX White Gold (XGM01)', 'gundam-marker-ex-white-gold', 850000, 60, 52, NULL, NULL, NULL, 'Bút sơn Marker dòng EX màu vàng trắng kim loại, độ che phủ cực tốt.', 'images/tools/marker-xgm01.jpg', 30, 1),

-- 53: Dụng cụ dán đề can
('Dung dịch làm mềm Decal Mr. Mark Softer', 'mr-mark-softer', 90000, 80, 53, NULL, NULL, NULL, 'Dung dịch Mr. Mark Softer giúp decal nước mềm ra và ôm sát vào các bề mặt gồ ghề của Gunpla.', 'images/tools/mr-mark-softer.jpg', 80, 1),

-- 54: Dụng cụ tách mảnh
('Dụng cụ tách part Wave Part Separator', 'wave-part-separator', 95000, 40, 54, NULL, NULL, NULL, 'Dụng cụ giúp tách các mảnh nhựa lỡ lắp sai mà không làm gãy chốt hay mẻ góc chi tiết.', 'images/tools/wave-separator.jpg', 40, 1),

-- 61: Sơn phủ bảo vệ
('Sơn phủ Mr. Super Clear Matt 170ml', 'mr-super-clear-matt', 180000, 60, 61, NULL, NULL, NULL, 'Sơn phủ bảo vệ (Topcoat) bề mặt nhám (Matt/Flat), giúp mô hình mất đi độ bóng nhựa giả tạo.', 'images/tools/mr-clear-matt.jpg', 250, 1),

-- 62: Keo dán mô hình
('Keo siêu mỏng Tamiya Extra Thin Cement (Xanh lá)', 'tamiya-extra-thin-cement', 110000, 120, 62, NULL, NULL, NULL, 'Keo dán Tamiya nắp xanh lá, độ loãng cao, chảy theo cơ chế mao dẫn, rất tốt để xóa khe hở (seam line).', 'images/tools/tamiya-extra-thin.jpg', 120, 1),

-- 71: Dao khắc
('Dao khắc Madworks Line Engraver 0.15mm', 'madworks-scriber-015', 380000, 20, 71, NULL, NULL, NULL, 'Dao khắc lằn chìm (Scriber) chuyên dụng cỡ 0.15mm, phù hợp với mô hình tỷ lệ 1/144 (HG/RG).', 'images/tools/madworks-015.jpg', 40, 1),

-- 73: Airbrush
('Súng phun sơn Iwata Eclipse HP-CS 0.3mm', 'iwata-eclipse-hp-cs', 3500000, 5, 73, NULL, NULL, NULL, 'Súng Airbrush nồi trên chuyên dụng Iwata Eclipse, kim béc 0.3mm, tiêu chuẩn cho dân sơn chuyên nghiệp.', 'images/tools/iwata-hpcs.jpg', 300, 1),

-- 81: Thảm cắt
('Thảm cắt Cutting Mat A3 Tamiya 74056', 'tamiya-cutting-mat-a3', 320000, 30, 81, NULL, NULL, NULL, 'Thảm cắt tự liền Tamiya khổ A3, bảo vệ mặt bàn và lưỡi dao, có in sẵn các thước đo góc tiện dụng.', 'images/tools/tamiya-mat-a3.jpg', 450, 1),

-- 82: Giá đỡ
('Action Base 4 Clear (Trong suốt)', 'action-base-4-clear', 180000, 150, 82, NULL, NULL, NULL, 'Giá đỡ Action Base 4 chính hãng Bandai màu trong suốt, dùng cho Gunpla 1/144 và 1/100.', 'images/tools/action-base-4.jpg', 200, 1),

-- 90: Combo khởi đầu
('Bộ dụng cụ cơ bản Bandai Spirits Entry Tool Set', 'bandai-spirits-entry-tool-set', 220000, 50, 90, NULL, NULL, NULL, 'Bộ công cụ nhập môn chính hãng Bandai gồm kềm cơ bản và nhíp, dành cho người mới bắt đầu.', 'images/tools/bandai-entry-set.jpg', 250, 1),

-- 41: Kềm cắt (Phân khúc giá rẻ & tầm trung)
('Kềm cắt Plato 170', 'kem-cat-plato-170', 45000, 200, 41, NULL, NULL, NULL, 'Kềm Plato giá rẻ, thích hợp cho người mới tập chơi dùng để cắt runner hoặc cắt dây kẽm mỏng.', 'images/tools/plato-170.jpg', 60, 1),
('Kềm Mineshima D-25 Premium Nipper', 'mineshima-d25-premium-nipper', 350000, 40, 41, NULL, NULL, NULL, 'Kềm lưỡi mỏng Mineshima sản xuất tại Nhật Bản, độ bền cao, cho vết cắt ngọt và đẹp.', 'images/tools/mineshima-d25.jpg', 100, 1),

-- 42: Dao trổ
('Dao trổ chuyên dụng OLFA AK-4', 'olfa-ak-4-art-knife', 280000, 30, 42, NULL, NULL, NULL, 'Dao trổ cao cấp OLFA AK-4 kèm các lưỡi dao đa năng dự phòng, tay cầm bọc cao su chống mỏi tay khi gọt ghẻ lâu.', 'images/tools/olfa-ak4.jpg', 80, 1),

-- 44: Dụng cụ mài nhám
('Bộ giấy nhám xốp DSPIAE (Siren)', 'dspiae-siren-sponge-sandpaper', 150000, 80, 44, NULL, NULL, NULL, 'Giấy nhám xốp DSPIAE có đánh số độ mịn rõ ràng từ #400 đến #1500, chống gập gãy, dễ dàng chà các bề mặt cong.', 'images/tools/dspiae-sponge.jpg', 50, 1),
('Bộ 3 dũa kim loại Tamiya Basic File Set', 'tamiya-basic-file-set-104', 180000, 45, 44, NULL, NULL, NULL, 'Bộ 3 dũa kim loại Tamiya (Bản dẹt, bán nguyệt, tròn) chuyên dùng để xử lý nhanh các vết cắt gờ nhựa cao.', 'images/tools/tamiya-basic-file.jpg', 120, 1),

-- 51: Bút kẻ viền
('Tamiya Panel Line Accent Color (Gray)', 'tamiya-panel-line-accent-gray', 120000, 100, 51, NULL, NULL, NULL, 'Dung dịch kẻ lằn chìm màu xám (Gray), cực kỳ phù hợp cho các part giáp màu trắng để tạo hiệu ứng tự nhiên, không bị quá gắt như màu đen.', 'images/tools/tamiya-panel-gray.jpg', 100, 1),
('Bút kẻ chảy tự động Gundam Marker GM301 (Black)', 'gundam-marker-gm301-pour-type', 75000, 150, 51, NULL, NULL, NULL, 'Bút kẻ lằn chìm dạng chảy tự động (Pour type), chỉ cần chấm nhẹ đầu bút vào rãnh, mực sẽ tự loang theo đường rãnh.', 'images/tools/marker-gm301.jpg', 30, 1),

-- 53: Dụng cụ dán đề can
('Keo dán hỗ trợ Decal Mr. Mark Setter', 'mr-mark-setter', 90000, 70, 53, NULL, NULL, NULL, 'Dung dịch tăng độ bám dính cực mạnh cho decal nước, giúp decal bám chắc vào bề mặt nhựa nhám, chống tróc theo thời gian.', 'images/tools/mr-mark-setter.jpg', 80, 1),

-- 61: Sơn phủ bảo vệ
('Sơn phủ Mr. Premium Topcoat Flat (Gốc nước)', 'mr-premium-topcoat-flat', 220000, 50, 61, NULL, NULL, NULL, 'Sơn phủ dạng xịt gốc nước Premium. Ưu điểm tuyệt đối là không làm chảy mực panel line hay ăn mòn hỏng decal, tạo hiệu ứng nhám mịn cực đẹp.', 'images/tools/mr-premium-flat.jpg', 250, 1),

-- 62: Keo dán mô hình
('Keo dán Tamiya Cement (Nắp trắng)', 'tamiya-cement-white', 110000, 80, 62, NULL, NULL, NULL, 'Keo dán mô hình dạng đặc, độ kết dính cực mạnh, thời gian khô chậm, lý tưởng để ghép chặt các part lớn lại với nhau.', 'images/tools/tamiya-cement-white.jpg', 120, 1),

-- 71: Dao khắc
('Mũi khắc rãnh BMC Chisel 0.2mm', 'bmc-chisel-02mm', 850000, 10, 71, NULL, NULL, NULL, 'Mũi khắc lằn chìm (Scriber) huyền thoại BMC cỡ 0.2mm làm từ thép Tungsten, mang lại độ sắc bén và chính xác tuyệt đối cho modder.', 'images/tools/bmc-02.jpg', 50, 1),

-- 82: Giá đỡ
('Action Base 1 (Black)', 'action-base-1-black', 160000, 120, 82, NULL, NULL, NULL, 'Giá đỡ Action Base 1 cỡ lớn chính hãng Bandai, thiết kế cực kỳ vững chắc, chuyên dụng cho mô hình MG (1/100) và các mẫu có trọng lượng nặng.', 'images/tools/action-base-1-black.jpg', 250, 1),
('Action Base 5 (Black)', 'action-base-5-black', 150000, 150, 82, NULL, NULL, NULL, 'Giá đỡ Action Base 5, thiết kế hiện đại, tinh gọn, rất phù hợp cho HG/RG (1/144), có khớp nối để ghép nhiều base tạo đội hình.', 'images/tools/action-base-5-black.jpg', 180, 1),

-- 83: Khay đựng linh kiện
('Khay nam châm phân loại part DSPIAE', 'dspiae-magnetic-parts-tray', 250000, 40, 83, NULL, NULL, NULL, 'Khay nhôm cao cấp đựng linh kiện tích hợp nam châm của hãng DSPIAE, chống thất lạc các part nhựa siêu nhỏ hoặc chi tiết kim loại.', 'images/tools/dspiae-tray.jpg', 300, 1),

-- 41: Kềm cắt chuyên dụng (4 sản phẩm)
('Kềm cắt DSPIAE ST-A 3.0 Single-blade', 'dspiae-st-a-30-nipper', 750000, 30, 41, NULL, NULL, NULL, 'Kềm cắt lưỡi đơn thế hệ 3.0 của DSPIAE, thép rèn nhiệt luyện siêu bén, đối thủ xứng tầm của Godhand SPN-120.', 'images/tools/dspiae-sta.jpg', 120, 1),
('Kềm Tamiya 74093 Modeler''s Side Cutter', 'tamiya-74093-modeler-cutter', 650000, 40, 41, NULL, NULL, NULL, 'Kềm cắt tiêu chuẩn của Tamiya, lưỡi sắc bén, độ bền cao dành cho người chơi lâu dài.', 'images/tools/tamiya-74093.jpg', 150, 1),
('Kềm cắt Bandai Build Up Nipper', 'bandai-build-up-nipper', 250000, 100, 41, NULL, NULL, NULL, 'Kềm chính hãng Bandai dành cho người mới, cắt nhựa êm, an toàn và dễ sử dụng.', 'images/tools/bandai-build-nipper.jpg', 100, 1),
('Kềm Mineshima D-29a', 'mineshima-d29a-nipper', 180000, 50, 41, NULL, NULL, NULL, 'Kềm nội địa Nhật Bản giá rẻ, lưỡi cắt thiết kế riêng cho việc tách mảnh nhựa khỏi runner.', 'images/tools/mineshima-d29a.jpg', 110, 1),

-- 42: Dao trổ (3 sản phẩm)
('Dao trổ Tamiya 74040 Modeler''s Knife', 'tamiya-74040-modeler-knife', 220000, 80, 42, NULL, NULL, NULL, 'Dao trổ đầu nhọn 30 độ huyền thoại của Tamiya, đi kèm hộp lưỡi dao thay thế.', 'images/tools/tamiya-74040.jpg', 60, 1),
('Dao trổ cán nhôm DSPIAE AT-TH', 'dspiae-at-th-aluminum-knife', 180000, 60, 42, NULL, NULL, NULL, 'Dao trổ cán nhôm nguyên khối DSPIAE, trọng tâm dồn về mũi dao giúp cắt ghẻ nhựa không tốn sức.', 'images/tools/dspiae-atth.jpg', 80, 1),
('Dao trổ OLFA 11B Art Knife', 'olfa-11b-art-knife', 150000, 100, 42, NULL, NULL, NULL, 'Dao cắt mỹ thuật OLFA (hãng sản xuất dao cho Tamiya), độ bén tuyệt hảo.', 'images/tools/olfa-11b.jpg', 50, 1),

-- 43: Nhíp gắp (3 sản phẩm)
('Nhíp thẳng Tamiya 74047 Straight Tweezers', 'tamiya-74047-straight-tweezers', 250000, 40, 43, NULL, NULL, NULL, 'Nhíp mũi thẳng Tamiya, chuyên dụng gắp các chi tiết nhựa nhỏ hoặc lò xo.', 'images/tools/tamiya-74047.jpg', 30, 1),
('Nhíp chống tĩnh điện DSPIAE AT-TZ', 'dspiae-at-tz-tweezers', 120000, 60, 43, NULL, NULL, NULL, 'Nhíp thép không gỉ DSPIAE sơn tĩnh điện, gắp decal không bị dính ngược vào nhíp.', 'images/tools/dspiae-attz.jpg', 30, 1),
('Nhíp thép không gỉ Vetus ST-11', 'vetus-st-11-tweezers', 50000, 150, 43, NULL, NULL, NULL, 'Nhíp Vetus giá rẻ mỏ nhọn siêu mảnh, dùng để gắp part hoặc nhúng decal nước.', 'images/tools/vetus-st11.jpg', 25, 1),

-- 44: Dụng cụ mài nhám (10 sản phẩm)
('Nhám xốp Tamiya Sanding Sponge 400', 'tamiya-sponge-400', 90000, 100, 44, NULL, NULL, NULL, 'Nhám xốp Tamiya độ nhám 400, chuyên phá ghẻ nhựa thô cứng.', 'images/tools/tamiya-sponge-400.jpg', 20, 1),
('Nhám xốp Tamiya Sanding Sponge 600', 'tamiya-sponge-600', 90000, 100, 44, NULL, NULL, NULL, 'Nhám xốp Tamiya độ nhám 600, bước thứ hai trong quy trình chà nhám Gunpla.', 'images/tools/tamiya-sponge-600.jpg', 20, 1),
('Nhám xốp Tamiya Sanding Sponge 1000', 'tamiya-sponge-1000', 90000, 100, 44, NULL, NULL, NULL, 'Nhám xốp Tamiya độ nhám 1000, làm mịn bề mặt trước khi phủ sơn hoặc topcoat.', 'images/tools/tamiya-sponge-1000.jpg', 20, 1),
('Nhám xốp Tamiya Sanding Sponge 1500', 'tamiya-sponge-1500', 90000, 100, 44, NULL, NULL, NULL, 'Nhám xốp Tamiya độ nhám 1500, dùng để đánh bóng nhẹ bề mặt nhựa.', 'images/tools/tamiya-sponge-1500.jpg', 20, 1),
('Thanh chà bóng Gunprimer Balancer White', 'gunprimer-balancer-white', 160000, 40, 44, NULL, NULL, NULL, 'Thanh nhám bọt biển cao cấp Gunprimer (Trắng), phục hồi độ bóng nguyên bản của nhựa.', 'images/tools/gunprimer-white.jpg', 30, 1),
('Thanh mài mờ Gunprimer Balancer Gray', 'gunprimer-balancer-gray', 160000, 40, 44, NULL, NULL, NULL, 'Thanh nhám Gunprimer (Xám), tạo hiệu ứng bề mặt nhám mờ đẹp mắt ngay lập tức.', 'images/tools/gunprimer-gray.jpg', 30, 1),
('Thanh dũa nam châm DSPIAE MS-01', 'dspiae-ms-01-magnetic-sander', 200000, 50, 44, NULL, NULL, NULL, 'Thanh dũa hợp kim nhôm dán giấy nhám bằng lực hút nam châm, luôn đảm bảo bề mặt chà phẳng tuyệt đối.', 'images/tools/dspiae-ms01.jpg', 90, 1),
('Giấy nhám xốp GodHand Kamiyasu 2mm Assortment', 'godhand-kamiyasu-2mm', 150000, 60, 44, NULL, NULL, NULL, 'Set nhám xốp GodHand siêu bền độ dày 2mm (đủ số 400/600/800/1000), chuyên uốn lượn bề mặt cong.', 'images/tools/godhand-kami-2mm.jpg', 40, 1),
('Giấy nhám xốp GodHand Kamiyasu 5mm Assortment', 'godhand-kamiyasu-5mm', 150000, 60, 44, NULL, NULL, NULL, 'Set nhám xốp GodHand độ dày 5mm, cầm chắc tay, lực chà đều.', 'images/tools/godhand-kami-5mm.jpg', 50, 1),
('Thanh dũa Infini Model Zebra', 'infini-model-zebra-stick', 65000, 120, 44, NULL, NULL, NULL, 'Thanh dũa xốp 2 mặt cứng cáp Infini Model, tiện dụng cho việc mài phẳng góc cạnh.', 'images/tools/infini-zebra.jpg', 20, 1),

-- 51: Bút kẻ viền (6 sản phẩm)
('Tamiya Panel Line Accent Color (Brown)', 'tamiya-panel-line-brown', 120000, 100, 51, NULL, NULL, NULL, 'Dung dịch kẻ lằn chìm màu Nâu, dùng cho các giáp màu vàng, đỏ hoặc cam.', 'images/tools/tamiya-panel-brown.jpg', 100, 1),
('Tamiya Panel Line Accent Color (Dark Gray)', 'tamiya-panel-line-dark-gray', 120000, 100, 51, NULL, NULL, NULL, 'Dung dịch kẻ lằn chìm màu Xám Đậm, thay thế màu đen để đường kẻ bớt gắt trên giáp trắng.', 'images/tools/tamiya-panel-darkgray.jpg', 100, 1),
('Gundam Marker GM02 (Gray Fine)', 'gundam-marker-gm02-gray', 65000, 150, 51, NULL, NULL, NULL, 'Bút kẻ viền ngòi kim màu xám GM02, mực ra đều, dễ dùng cho newbie.', 'images/tools/marker-gm02.jpg', 30, 1),
('Gundam Marker GM03 (Brown Fine)', 'gundam-marker-gm03-brown', 65000, 150, 51, NULL, NULL, NULL, 'Bút kẻ viền ngòi kim màu nâu GM03.', 'images/tools/marker-gm03.jpg', 30, 1),
('Gundam Marker GM302 (Gray Pour Type)', 'gundam-marker-gm302-gray-pour', 75000, 150, 51, NULL, NULL, NULL, 'Bút kẻ lằn chảy GM302 màu Xám, mực tự động loang theo rãnh nhựa.', 'images/tools/marker-gm302.jpg', 30, 1),
('Gundam Marker GM303 (Brown Pour Type)', 'gundam-marker-gm303-brown-pour', 75000, 150, 51, NULL, NULL, NULL, 'Bút kẻ lằn chảy GM303 màu Nâu.', 'images/tools/marker-gm303.jpg', 30, 1),

-- 52: Bút sơn (6 sản phẩm)
('Gundam Marker EX Shine Silver (XGM02)', 'marker-ex-shine-silver', 85000, 80, 52, NULL, NULL, NULL, 'Bút sơn Marker EX màu bạc sáng bóng, sơn lên ống đồng hoặc khung xương cực đẹp.', 'images/tools/marker-xgm02.jpg', 30, 1),
('Gundam Marker EX Heavy Gun Metallic (XGM03)', 'marker-ex-heavy-gun-metallic', 85000, 80, 52, NULL, NULL, NULL, 'Bút sơn Marker EX màu kim loại súng (Gunmetal), màu tối nhám.', 'images/tools/marker-xgm03.jpg', 30, 1),
('Gundam Marker EX Cosmo M Blue (XGM04)', 'marker-ex-cosmo-blue', 85000, 60, 52, NULL, NULL, NULL, 'Bút sơn Marker EX màu xanh biển ánh kim Metallic.', 'images/tools/marker-xgm04.jpg', 30, 1),
('Gundam Marker Eraser (GM300)', 'gundam-marker-eraser-gm300', 60000, 200, 52, NULL, NULL, NULL, 'Bút tẩy mực Marker GM300, dùng để xóa các vết lem sơn hoặc mực kẻ lằn thừa.', 'images/tools/marker-gm300.jpg', 30, 1),
('Bộ 6 bút sơn Gundam Marker Basic Set', 'gundam-marker-basic-set', 350000, 30, 52, NULL, NULL, NULL, 'Set 6 cây bút sơn cơ bản (Đỏ, Xanh, Vàng, Trắng, Gunmetal, Kẻ viền đen).', 'images/tools/marker-basic-set.jpg', 180, 1),
('Bộ 6 bút sơn Zeon Marker Set', 'zeon-marker-set', 350000, 25, 52, NULL, NULL, NULL, 'Set 6 cây bút sơn tông màu Zeon (Đỏ Char, Xanh Zaku, Xanh lá đậm...).', 'images/tools/marker-zeon-set.jpg', 180, 1),

-- 53: Dụng cụ dán đề can (3 sản phẩm)
('Dung dịch dán decal Tamiya Mark Fit', 'tamiya-mark-fit-standard', 85000, 100, 53, NULL, NULL, NULL, 'Dung dịch Tamiya Mark Fit tiêu chuẩn, làm mềm và tăng độ bám cho decal nước.', 'images/tools/tamiya-mark-fit.jpg', 70, 1),
('Dung dịch dán decal Tamiya Mark Fit (Strong)', 'tamiya-mark-fit-strong', 95000, 80, 53, NULL, NULL, NULL, 'Dung dịch Mark Fit nắp vàng (Strong), dùng cho decal dày cứng đầu bám vào bề mặt cong/gồ ghề.', 'images/tools/tamiya-mark-fit-strong.jpg', 70, 1),
('Khay ngâm decal nước DSPIAE', 'dspiae-decal-tray', 180000, 40, 53, NULL, NULL, NULL, 'Khay ngâm decal chuyên dụng DSPIAE tích hợp đệm bọt biển giữ ẩm lâu.', 'images/tools/dspiae-decal-tray.jpg', 150, 1),

-- 54: Dụng cụ tách mảnh (1 sản phẩm)
('Dụng cụ tách mảnh DSPIAE PT-MPS', 'dspiae-pt-mps-separator', 150000, 60, 54, NULL, NULL, NULL, 'Nạy tách mảnh nhựa DSPIAE làm bằng thép không gỉ siêu mỏng, không làm xước cạnh nhựa.', 'images/tools/dspiae-ptmps.jpg', 50, 1),

-- 61: Sơn phủ bảo vệ (4 sản phẩm)
('Sơn phủ Mr. Super Clear Gloss (B513)', 'mr-super-clear-gloss', 190000, 50, 61, NULL, NULL, NULL, 'Chai xịt Topcoat tạo độ bóng sáng (Gloss) gốc dung môi, giúp mô hình bóng lộn như sơn xe hơi.', 'images/tools/mr-super-clear-gloss.jpg', 250, 1),
('Sơn phủ Mr. Super Clear Semi-Gloss (B514)', 'mr-super-clear-semi-gloss', 190000, 50, 61, NULL, NULL, NULL, 'Chai xịt Topcoat tạo độ bóng mờ (Semi-Gloss) gốc dung môi, giữ nguyên độ bóng gốc của nhựa Bandai.', 'images/tools/mr-super-clear-semi.jpg', 250, 1),
('Sơn phủ Mr. Premium Topcoat Gloss (B501)', 'mr-premium-topcoat-gloss', 220000, 50, 61, NULL, NULL, NULL, 'Chai xịt Topcoat Gloss gốc nước Premium, an toàn tuyệt đối với mọi loại sơn và decal bên dưới.', 'images/tools/mr-premium-gloss.jpg', 250, 1),
('Sơn phủ Mr. Premium Topcoat Semi-Gloss (B502)', 'mr-premium-topcoat-semi-gloss', 220000, 50, 61, NULL, NULL, NULL, 'Chai xịt Topcoat bóng mờ gốc nước Premium.', 'images/tools/mr-premium-semi.jpg', 250, 1),

-- 62: Keo dán mô hình (3 sản phẩm)
('Keo siêu lỏng Tamiya Extra Thin Quick-Setting', 'tamiya-extra-thin-quick-setting', 135000, 80, 62, NULL, NULL, NULL, 'Keo dán Tamiya Extra Thin nắp xanh lá nhạt, tốc độ khô siêu nhanh chỉ trong vài giây.', 'images/tools/tamiya-extra-thin-quick.jpg', 120, 1),
('Keo dán lỏng Mr. Cement SP (Đen)', 'mr-cement-sp-black', 120000, 80, 62, NULL, NULL, NULL, 'Keo dán siêu chảy Mr. Cement Super Power, kết dính nhựa ABS/PS cực mạnh.', 'images/tools/mr-cement-sp.jpg', 120, 1),
('Keo dán Mr. Cement S (Xanh dương)', 'mr-cement-s-blue', 100000, 80, 62, NULL, NULL, NULL, 'Keo dán lỏng chảy theo khe hở (tương đương Tamiya nắp xanh lá).', 'images/tools/mr-cement-s.jpg', 120, 1),

-- 71: Dao khắc rãnh (3 sản phẩm)
('Mũi khắc rãnh DSPIAE Push Broach 0.15mm', 'dspiae-push-broach-015', 350000, 20, 71, NULL, NULL, NULL, 'Mũi dao khắc lằn chìm Tungsten DSPIAE cỡ 0.15mm.', 'images/tools/dspiae-broach-015.jpg', 30, 1),
('Mũi khắc rãnh DSPIAE Push Broach 0.3mm', 'dspiae-push-broach-030', 350000, 20, 71, NULL, NULL, NULL, 'Mũi dao khắc lằn chìm Tungsten DSPIAE cỡ 0.3mm (Dùng cho tỷ lệ 1/100).', 'images/tools/dspiae-broach-030.jpg', 30, 1),
('Băng dính dẫn hướng khắc rãnh Madworks 3mm', 'madworks-carving-tape-3mm', 140000, 50, 71, NULL, NULL, NULL, 'Băng dính Carving Tape cứng và dày dặn, giúp mũi dao khắc đi theo đường thẳng tuyệt đối không bị trượt.', 'images/tools/madworks-tape.jpg', 40, 1),

-- 81: Thảm cắt (1 sản phẩm)
('Thảm cắt DSPIAE A4 Cutting Mat (Black)', 'dspiae-a4-cutting-mat', 250000, 40, 81, NULL, NULL, NULL, 'Thảm cắt tự liền DSPIAE khổ A4 màu đen sang trọng, chất liệu PVC đàn hồi cao.', 'images/tools/dspiae-a4-mat.jpg', 300, 1),

-- 82: Giá đỡ (2 sản phẩm)
('Action Base 2 (Black)', 'action-base-2-black', 130000, 150, 82, NULL, NULL, NULL, 'Giá đỡ Action Base 2 chuyên dụng cho mô hình tỷ lệ nhỏ gọn (HG/RG 1/144).', 'images/tools/action-base-2-black.jpg', 150, 1),
('Action Base 6 (Clear)', 'action-base-6-clear', 180000, 120, 82, NULL, NULL, NULL, 'Action Base 6 (Bộ 2 đế) siêu gọn nhẹ của Bandai, thiết kế mới nhất tối ưu hiển thị, tàng hình hoàn hảo.', 'images/tools/action-base-6-clear.jpg', 180, 1),

-- 83: Khay đựng linh kiện (1 sản phẩm)
('Đế cắm kẹp sơn mô hình (Paint Clip Base)', 'paint-clip-base', 80000, 100, 83, NULL, NULL, NULL, 'Đế xốp carton chuyên dụng có đục lỗ sẵn để cắm que kẹp phơi khô các part sau khi xịt sơn.', 'images/tools/paint-clip-base.jpg', 200, 1);