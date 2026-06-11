@extends('layouts.app')

@section('title', 'تسجيل الدخول')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    <div class="surface-card p-4 p-md-5">
                        <p class="fw-bold text-accent mb-2">مرحبًا بعودتك</p>
                        <h1 class="h2 fw-bold mb-4">تسجيل الدخول</h1>

                        <form method="POST" action="{{ route('login.store') }}" class="d-grid gap-3">
                            @csrf

                            <div>
                                <label class="form-label" for="email">البريد الإلكتروني</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="password">كلمة المرور</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <label class="form-check">
                                <input class="form-check-input" name="remember" type="checkbox" value="1">
                                <span class="form-check-label">تذكرني</span>
                            </label>

                            <button class="btn btn-brand btn-lg" type="submit">دخول</button>
                        </form>

                        <p class="text-muted-soft mt-4 mb-0">
                            ليس لديك حساب؟
                            <a class="text-accent fw-bold" href="{{ route('register') }}">أنشئ حسابًا</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
