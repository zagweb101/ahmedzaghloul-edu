@extends('layouts.app')

@section('title', 'إنشاء حساب')

@section('content')
    <section class="section-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-5">
                    <div class="surface-card p-4 p-md-5">
                        <p class="fw-bold text-accent mb-2">ابدأ رحلتك</p>
                        <h1 class="h2 fw-bold mb-4">إنشاء حساب</h1>

                        <form method="POST" action="{{ route('register.store') }}" class="d-grid gap-3">
                            @csrf

                            <div>
                                <label class="form-label" for="name">الاسم</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="email">البريد الإلكتروني</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required>
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

                            <div>
                                <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required>
                            </div>

                            <button class="btn btn-brand btn-lg" type="submit">إنشاء الحساب</button>
                        </form>

                        <p class="text-muted-soft mt-4 mb-0">
                            لديك حساب بالفعل؟
                            <a class="text-accent fw-bold" href="{{ route('login') }}">سجل الدخول</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
