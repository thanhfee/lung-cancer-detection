<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; // Đảm bảo có dòng này
use Illuminate\Support\Facades\Auth; // Đảm bảo có dòng này

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    // Sửa lại khai báo hàm: ép kiểu trả về rõ ràng là : Response để Laravel Pipeline nhận diện
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục!');
        }

        // 2. Nếu là Admin, chặn lại không cho quét ảnh và đẩy về trang trước
        if (Auth::user()->role === 'admin') {
            return redirect()->back()->with('error', 'Tài khoản Quản trị viên (Admin) không có chức năng thực hiện quét ảnh y khoa!');
        }

        // 3. Đúng là tài khoản hợp lệ thì cho đi tiếp
        return $next($request);
    }
}