{{-- resources/views/menu.blade.php --}}

<nav class="navbar navbar-expand-xl bg-white shadow-sm border-0 rounded-0 p-0 m-0">
    <div class="container-fluid p-0">
        <ul class="navbar-nav flex-row w-100 justify-content-start gap-2">
            <li class="nav-item">
                <a href="{{ url('/admin/category') }}"
                   class="nav-link d-flex align-items-center py-3 px-4 {{ Request::is('admin/category') ? 'active' : '' }}">
                    <span class="me-2"><i class="fa-solid fa-list" style="color: #63E6BE;"></i></span>
                    <span class="d-none d-md-inline">Danh mục</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/admin/recipe') }}"
                   class="nav-link d-flex align-items-center py-3 px-4 {{ Request::is('admin/recipe') ? 'active' : '' }}">
                    <span class="me-2"><i class="fa-solid fa-drumstick-bite" style="color: #FFD43B;"></i></span>
                    <span class="d-none d-md-inline">Quản lý Công thức</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/admin/user') }}"
                   class="nav-link d-flex align-items-center py-3 px-4 {{ Request::is('admin/user') ? 'active' : '' }}">
                    <span class="me-2"><i class="fa-solid fa-user" style="color: #008bf5;"></i></span>
                    <span class="d-none d-md-inline">Quản lý người dùng</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

{{-- Phần <script> và <style> của Vue component KHÔNG được đưa vào đây. --}}
