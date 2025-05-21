{{-- resources/views/top.blade.php --}}

<header class="shadow-sm bg-white sticky-top py-0 px-3">
    <nav class="navbar navbar-expand align-items-center px-0 py-0">
        <div class="d-flex align-items-center flex-shrink-0">
            {{-- Logo Image --}}
            {{-- Đường dẫn hình ảnh được chuyển đổi sang sử dụng helper asset() --}}
            {{-- Giả định tệp nằm tại public/assets/assets_rocker/images/lau.png --}}
            <img
                src="{{ asset('assets/images/lau.png') }}"
                alt="logo icon"
                class="d-inline-block align-middle me-2"
                style="width:48px; height:48px; object-fit:contain;"
            >
            <h4 class="mb-0 fw-semibold text-nowrap d-none d-sm-block" style="font-size:1.2em;">
                Admin LTE
                {{-- Nếu bạn muốn tiêu đề này động, có thể dùng biến Blade: {{ $app_name ?? 'Admin Panel' }} --}}
            </h4>
        </div>
        <div class="flex-grow-1 text-center d-none d-md-flex align-items-center justify-content-center">
            <span class="fw-bold text-dark text-truncate"
                style="font-size:2.2rem;font-family:'Franklin Gothic Medium','Arial Narrow',Arial,sans-serif;">
                Food Recipe Nghia
                {{-- Nếu bạn muốn tiêu đề này động, có thể dùng biến Blade: {{ $site_title ?? 'Website Title' }} --}}
            </span>
        </div>
        <div class="dropdown ms-auto d-flex align-items-center" style="height:60px;">
            <a
                class="nav-link dropdown-toggle p-0 d-flex align-items-center"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                {{-- User Avatar Image --}}
                {{-- Đường dẫn hình ảnh được chuyển đổi sang sử dụng helper asset() --}}
                {{-- Giả định tệp nằm tại public/assets/assets_rocker/images/avatars/avatar-2.png --}}
                <img
                    src="{{ asset('assets/images/avatars/avatar-2.png') }}"
                    alt="user avatar"
                    class="rounded-circle border border-secondary"
                    style="width:38px; height:38px; object-fit:cover;"
                >
                <div class="ms-2 d-none d-sm-block">
                    {{-- Hiển thị tên người dùng. Sử dụng biến Blade $ten_hien_thi. --}}
                    {{-- Cần truyền biến này khi @include partial, hoặc dùng Auth::user() nếu phù hợp. --}}
                    <p class="mb-0 fw-medium text-dark">{{ $ten_hien_thi ?? (Auth::check() ? Auth::user()->name : 'Guest') }}</p>
                    {{-- Chức vụ người dùng (static trong Vue code gốc) --}}
                    {{-- Nếu muốn động, dùng biến Blade: {{ $chuc_vu ?? (Auth::check() ? (Auth::user()->chuc_vu ?? 'User') : '') }} --}}
                    <small class="text-muted d-none d-md-block">Web Designer</small>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    {{-- Link Đăng Xuất - Kích hoạt submit form --}}
                    {{-- @click="dangXuat" được thay bằng onclick để submit form 'logout-form' --}}
                    {{-- href="javascript:;" được thay bằng href="#" --}}
                    <a class="dropdown-item" href="#"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bx bx-log-out-circle me-2"></i>Đăng Xuất
                    </a>
                    {{-- LƯU Ý: Form có ID 'logout-form' PHẢI được đặt ở file layout chính (master.blade.php) --}}
                    {{-- để tránh lặp mã HTML form trên cùng một trang. --}}
                </li>
                 {{-- Link Đăng Xuất Tất Cả Thiết Bị - Kích hoạt submit form --}}
                 {{-- @click="dangXuatAll" được thay bằng onclick để submit form 'logout-all-form' --}}
                 {{-- href="javascript:;" được thay bằng href="#" --}}
                 {{-- Kiểm tra xem route 'logout.all' có tồn tại không trước khi hiển thị link --}}
                 @if(Route::has('logout.all'))
                <li>
                    <a class="dropdown-item" href="#"
                       onclick="event.preventDefault(); document.getElementById('logout-all-form').submit();">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng Xuất Tất Cả Thiết Bị
                    </a>
                    {{-- LƯU Ý: Form có ID 'logout-all-form' PHẢI được đặt ở file layout chính (master.blade.php) --}}
                    {{-- để tránh lặp mã HTML form trên cùng một trang. --}}
                 </li>
                 @endif
            </ul>
        </div>
    </nav>
</header>

{{-- Phần <script> và <style> của Vue component KHÔNG được đưa vào đây. --}}
{{-- Logic JavaScript của Vue component cần được viết lại nếu bạn vẫn muốn sử dụng JS cho các hành động khác ngoài logout form submit. --}}
{{-- Các form 'logout-form' và 'logout-all-form' cần được định nghĩa ở file master layout chính. --}}
