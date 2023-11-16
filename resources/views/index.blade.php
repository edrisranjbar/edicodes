@section('title', 'توسعه دهنده بک اند وب')
@extends('layouts.app')
@section('content')
<main class="container-fluid">
    <section class="hero row">
        <div class="profile-section col-12 col-lg-6 col-xxl-6">
            <img src="{{ $widgets['hero']['image']['src'] }}" alt="{{ $widgets['hero']['image']['alt'] }}"
                class="profile">
            <div class="circle-sm my-bg-primary rounded-circle left-top"></div>
            <div class="circle-sm bg-orange rounded-circle right-top"></div>
            <div class="circle-md bg-orange rounded-circle right-bottom"></div>
        </div>
        <div class="hero-details-section col-12 col-xxl-6">
            <p class="hero-subtitle">{{ $widgets['hero']['subtitle'] }}</p>
            <p class="hero-title">{!! $widgets['hero']['title'] !!}</p>
            <p class="hero-description">{!! $widgets['hero']['description'] !!}</p>
            <div class="d-flex gap-2 text-light">
                <a class="btn btn-lg btn-primary" href="{{ $coursesUrl }}">مشاهده دوره ها</a>
                <a class="btn btn-lg btn-outline-secondary" href="{{ $blogUrl }}">وبلاگ</a>
            </div>
        </div>
        <div class="students">
            <p>کارآموزان دوره ها</p>
            <div class="d-flex">
                <div class="student-profiles">
                    @foreach($widgets['hero']['students'] as $student)
                    <img src="{{ $student['src'] }}" class="student-profile">
                    @endforeach
                </div>
                <span style="direction: ltr;">+{{ count($widgets['hero']['students']) }}</span>
            </div>
        </div>
    </section>
    <section class="d-flex justify-content-center">
        <img src="{{ asset('images/arrow.svg') }}" class="arrow">
    </section>
    <section id="skills">
        <h2 class="title">مهارت های من</h2>
        <div class="breaker"></div>
        <div class="skills-box gy-4 row">
            <div class="col-6 col-md-3">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/git.png') }}" alt="Git">
                        </div>
                        <p>کنترل پروژه</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/programming.png') }}" alt="Git">
                        </div>
                        <p>برنامه نویسی</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/server.png') }}" alt="Git">
                        </div>
                        <p>لینوکس</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/teaching.png') }}" alt="Git">
                        </div>
                        <p>آموزش</p>
                    </div>
                </div>
            </div>
        </div>
        <img src="{{ asset('images/waves.svg') }}" class="waves">
    </section>
    <section id="tutorials" class="row">
        <div class="tutorials-left col-xl-6 col-12">
            <h2 class="title">دوره های آموزشی</h2>
            <div class="breaker"></div>
            <p class="tutorials-section-description">
                من توی دوره های آموزشیم سعی میکنم مفاهیم و مهارت ها رو به صورت واضح و ساده بیان کنم. تا جای ممکن هم
                پروژه محور پیش میریم که براتون کاربردی باشه.
            </p>
            <a href="#" class="btn btn-lg btn-outline-primary text-light d-inline-block">
                <i class="fa fa-eye me-1 btn-w-icon"></i>
                مشاهده همه
            </a>
        </div>
        <div class="col-xl-6 col-12 splide tutorials-right" role="group" aria-label="اسلایدر دوره ها">
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach($tutorials as $tutorial)
                        <li class="card tutorial splide__slide">
                            <a href="{{ $tutorial->link }}">
                                @if ($tutorial->thumbnail)
                                    <img src="{{ asset('storage/upload/' . $tutorial->thumbnail) }}" class="thumbnail" alt="{{ $tutorial->title }}">
                                @endif
                                <h3 class="post-title">{{ $tutorial->title }}</h3>
                            </a>
                            <div class="d-flex post-meta">
                                <a href="{{ $tutorial->link }}" class="btn btn-sm btn-primary">مشاهده</a>
                                <div class="d-flex align-items-center students-count">
                                    <i class="fas fa-user-circle"></i>
                                    دانشجویان: {{ $tutorial->students()->count() }}
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <section id="about">
        <div class="profile-box mx-auto"></div>
        <h2 class="title text-center">درباره ادریس رنجبر</h2>
        <p class="text-center">
            ســــلــااام✋،
            به وب سایت من خوش اومدی. من ادریس رنجبر هستم، یه عدد برنامه نویس یا به عبارت دقیق تر توسعه دهنده بک اند
            وب.
            کار اصلیم ساخت و توسعه وب سایت و وب اپلیکیشینه و در کنارش آموزش هم میدیم. خیلی وقت ها به صورت فریلنسری و
            فول استک کار میکنم.
            بیشتر وقتم را در دنیای کدنویسی و طراحی وب سپری می‌کنم. علاقه‌مند به به اشتراک گذاری تجربیات و مهارت‌هایم
            هستم و همیشه در
            تلاش برای یادگیری و بهبود خودم هستم. در کنار کارهای توسعه وب، من یک طبیعت‌گرد هم هستم و از ارتباط با
            طبیعت و طبیعت گردی لذت می‌برم. 🌿
        </p>
    </section>
    <section id="blog" class="row">
        <div class="col-xl-6 col-12 blog-left">
            <h2 class="title">جدیدترین نوشته ها</h2>
            <div class="breaker"></div>
            <p class="blog-section-description">این یک متن تستیه، این یک متن تستیه، این یک متن تستیه، این یک
                متن تستیه، این یک متن تستیه...</p>
        </div>
        <div class="col-xl-6 col-12 splide blog-right" role="group" aria-label="اسلایدر نوشته ها">
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach($posts as $post)
                        <li class="card blog splide__slide">
                            <a href="{{ $post->link }}">
                                <img src="{{ asset('storage/upload/' . $post->thumbnail) }}" class="thumbnail" alt="{{ $post->title }}">
                            </a>
                            <h3 class="post-title">{{ $post->title }}</h3>
                            <div class="d-flex post-meta">
                                <a href="{{ $post->link }}" class="btn btn-sm btn-primary">مشاهده</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <section id="contact">
        <h2 class="title text-center">اگه پروژه ای چیزی داشتی یا باهام کاری داشتی👇</h2>
        <div class="form-container">
            <div class="background">
                <div class="screen">
                    <div class="screen-header">
                        <div class="screen-header-left">
                            <div class="screen-header-btn close"></div>
                            <div class="screen-header-btn maximize"></div>
                            <div class="screen-header-btn minimize"></div>
                        </div>
                        <div class="screen-header-right">
                            <div class="screen-header-ellipsis"></div>
                            <div class="screen-header-ellipsis"></div>
                            <div class="screen-header-ellipsis"></div>
                        </div>
                    </div>
                    <div class="screen-body">
                        <div class="screen-body-item left">
                            <div class="app-title">
                                <span>تماس با من</span>
                            </div>
                            <br>
                            <div class="contact-info">
                                <p>
                                    اگه کاری داشتی یا به هر دلیلی دلت تنگ شد و خواستی باهام صحبت کنی😂
                                    میتونی بهم ایمیل بدی. معمولا زود جواب میدم.
                                </p>
                                <br>
                                <address>
                                    ایمیل: <a href="mailto:edris.qeshm2@gmail.com"
                                        class="email-link">edris.qeshm2@gmail.com</a>
                                    <br>
                                    لینکدین: <a href="https://ir.linkedin.com/in/edris-ranjbar"
                                        class="email-link">edris-ranjbar</a>
                                    <br>
                                    توییتر: <a href="https://twitter.com/edris__ranjbar" class="email-link">
                                        edris__ranjbar
                                    </a>
                                </address>
                            </div>
                        </div>
                        <div class="screen-body-item">
                            <div class="app-form">
                                <div class="app-form-group">
                                    <input class="app-form-control" placeholder="نام و نام خانوادگی">
                                </div>
                                <div class="app-form-group">
                                    <input class="app-form-control" placeholder="ایمیل">
                                </div>
                                <div class="app-form-group message">
                                    <input class="app-form-control" placeholder="متن پیام">
                                </div>
                                <btn class="btn btn-sm btn-outline-secondary text-white btn-w-icon">
                                    <i class="fa fa-send ml-1"></i>
                                    ثبت فرم
                                </btn>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</main>
@stop()