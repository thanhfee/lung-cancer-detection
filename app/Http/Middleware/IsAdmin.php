<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // Kiểm tra xem user đã đăng nhập và có role là admin không
    if (auth()->check() && auth()->user()->role === 'admin') {
        return $next($request);
    }

    // Nếu không phải admin, trả về trang trước kèm thông báo lỗi
    return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này!');
}
}
