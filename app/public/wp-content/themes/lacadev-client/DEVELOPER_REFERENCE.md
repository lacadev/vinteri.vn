# 📘 Tài Liệu Hướng Dẫn Phát Triển (Developer Reference)
*Theme: LacaDev Client*

Tài liệu này tổng hợp các cấu trúc cơ bản, các class, function và hook quan trọng nhất trong hệ thống. Dành cho các Developer mới tiếp cận dự án, giúp bạn biết nên dùng hàm nào có sẵn, viết code vào đâu và tuân theo tiêu chuẩn nào.

---

## 1. 🛠 Các Hàm Tiện Ích (Helpers) - `app/helpers/`
*Giúp xử lý các tác vụ lặp đi lặp lại. Thay vì code lại từ đầu, hãy gọi các hàm này.*

- **`getOption($name)`**
  - **Vị trí:** `app/helpers/template_tags.php`
  - **Mục đích:** Lấy giá trị cài đặt trong Theme Options (Carbon Fields) tự động nối với mã ngôn ngữ hiện tại.
  - **Cách dùng:** `$logo = getOption('site_logo');`

- **`theResponsivePostThumbnail($size, $attr)`**
  - **Vị trí:** `app/helpers/responsive-images.php`
  - **Mục đích:** Render ảnh đại diện của bài viết chuẩn Responsive (auto tạo `<picture>`, `srcset`, định dạng WebP). Bắt buộc dùng hàm này để tối ưu tốc độ load.
  - **Cách dùng:** `theResponsivePostThumbnail('mobile', ['class' => 'img-fluid']);`

- **`getPageTitle()`**
  - **Vị trí:** `app/helpers/template_tags.php`
  - **Mục đích:** Tự động trả về tiêu đề chuẩn xác của trang hiện tại (tự nhận diện Page, Archive, Search, Taxonomy hay Single).
  - **Cách dùng:** `echo getPageTitle();`

- **`theAsset($path)`**
  - **Vị trí:** `app/helpers/template_tags.php`
  - **Mục đích:** Xuất src tuyệt đối tới các file tĩnh (ảnh, icon) trong thư mục `resources/`.
  - **Cách dùng:** `<img src="<?php theAsset('images/logo.svg'); ?>">`

---

## 2. ⚙️ Cấu Hình Khởi Tạo Theme (Theme Setup) - `theme/setup/`
*Quản lý cấu hình cốt lõi của WordPress, CPT, Menu và Security.*

- **`theme-support.php`**
  - **Mục đích:** Khai báo cấu hình `add_theme_support` lúc init, đăng ký cắt sizes ảnh tự động, đăng ký các vị trí Navigation Menus. Thêm cấu hình hệ thống vào đây.

- **Class `Laca_Menu_Walker`** (`theme/setup/walkers/Laca_Menu_Walker.php`)
  - **Mục đích:** Viết đè (override) cấu trúc sinh ra DOM HTML của menu WordPress. Dùng khi bạn cần menu render ra chuẩn class của TailwindCSS (bọc thẻ div, thẻ li custom) hoặc làm Mega-Menu.

- **Class `Laca_Recaptcha`** (`theme/setup/recaptcha.php`)
  - **Mục đích:** Đảm nhiệm xử lý xác thực Google reCAPTCHA v3. Các form comment, form login được tự động nhúng reCAPTCHA để chống spam.

---

## 3. 🧩 Core Logic & API (Backend Custom) - `app/src/`
*Các Class viết chuẩn OOP chuyên trị logic, API và Database (Không code procedural ở đây).*

- **`ProjectFields::register()`** (`app/src/Features/ProjectManagement/`)
  - **Mục đích:** Đăng ký các trường mở rộng (Meta fields / Carbon fields) đặc thù cho Custom Post Type `project`. Nơi quản lý dữ liệu thông tin dự án.
  
- **`AITranslationManager::handleAITranslateRequest()`** (`app/src/Settings/LacaTools/`)
  - **Mục đích:** API Endpoint nội bộ để lắng nghe request từ Editor truyền lên, xử lý việc dịch bài viết tự động qua dịch vụ AI.
  
- **`TrackerEndpointHandler::handleIncomingLog()`**
  - **Mục đích:** API thu thập log/tracking dữ liệu hoạt động gửi về từ hệ thống client.

---

## 4. 🧱 Gutenberg Blocks - `block-gutenberg/`
*Kiến trúc khối giao diện. Mỗi block được modular hóa nằm trong 1 folder độc lập.*

**Cấu trúc 1 Block:**
- `block.json`: Nơi khai báo tên, icon, và attributes (dữ liệu biến).
- `index.js`, `edit.js`, `save.js`: Mã nguồn React quản lý hiển thị trải nghiệm Kéo-thả trong luồng Admin (wp-admin).
- `render.php`: Luồng render giao diện thực tế (Frontend). Được kết hợp code PHP và HTML.
- `style.scss`: Style độc lập của khối đó dùng Tailwind/SCSS.

**Tham khảo cách code từ các Blocks mẫu:**
- **`hero-block`**: Xem cách xử lý Layout nền lớn chuẩn xác.
- **`service-block` & `project-block`**: Học cách lấy ID/dữ liệu từ Editor truyền xuống PHP Query để list bài viết ra trang chủ.
- **`tech-list-block`**: Ví dụ cách dùng mảng Repeaters từ Edit.js render ra list trong `render.php`.

---

## 5. 🎨 Frontend Scripts - `resources/scripts/`
*Toàn bộ logic Javascript tĩnh cho người dùng cuối (Vanilla JS).*

- **`initContactPage()`** (`theme/pages/contact.js`): Xử lý form liên hệ, bắt validate, lấy token reCaptcha và AJAX sang backend.
- **`initCommentForm()`** (`theme/pages/comments.js`): Xử lý AJAX mượt mà khi người dùng thêm bình luận (không reload lại trang).
- **`updateCounter()`** (`theme/micro-interactions.js`): Hiệu ứng đếm số chạy từ 0 đến N khi scroll màn hình. Dùng cho thẻ hiển thị số liệu.
- **`handleLoadMore()`** (`theme/pages/search.js`): Tính năng "Tải thêm" (Load More) list bài viết dùng AJAX + Fetch API.

---

### 💡 Quy Tắc Bắt Buộc Khi Thêm Mới Code:
1. **Tái sử dụng Helpers:** Khi thao tác gọi ảnh, bắt buộc gọi `theResponsivePostThumbnail()` (tuyệt đối không tự viết thẻ `<img>`). Tương tự, dùng `getOption()` thay vì hàm mặc định.
2. **Không làm rác `functions.php`:** 
   - Functions.php chỉ dùng để load thư viện.
   - Các hook nhỏ bổ sung hãy để ở `app/hooks.php`.
   - Các logic phức tạp trên 50 dòng phải tạo Class trong thư mục `app/src/`.
3. **CSS / JS:**
   - Javascript ưu tiên thuần túy (Vanilla JS), không gọi jQuery để load web tĩnh tốc độ cao.
   - CSS ứng dụng triệt để Tailwind. Các Custom CSS viết rời ở từng file cục bộ trong thư mục components hoặc Blocks, không dồn hết một file.
