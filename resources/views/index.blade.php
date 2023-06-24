@section('title', 'توسعه دهنده بک اند وب')
@extends('layouts.app')
@section('content')
<main>
    <section class="hero row">
        <div class="profile-section col-xxl-6 col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <img src="{{ asset('images/profile.png') }}" alt="ادریس رنجبر" class="profile">
            <div class="circle-sm my-bg-primary rounded-circle left-top"></div>
            <div class="circle-sm bg-orange rounded-circle right-top"></div>
            <div class="circle-md bg-orange rounded-circle right-bottom"></div>
        </div>
        <div class="hero-details-section col-xxl-6 col-xl-12 col-lg-12 col-sm-12 col-xs-12">
            <p class="hero-subtitle">ســــلــااام، مــن</p>
            <p class="hero-title">
                ادریس رنجبــر&nbsp;
                <span>هستــم</span>
            </p>
            <p class="hero-description">
                توسعه دهنده بک اند وب، مدرس و طبیعت گرد. علاقه&nbsp;مند به اشتراک تجربیات ومهارت ها.</p>
            <div class="d-flex button-group">
                <a class="btn btn-lg button-primary" href="#">مشاهده دوره ها</a>
                <a class="btn btn-lg btn-outline-secondary text-white" href="#">وبلاگ</a>
            </div>
        </div>
        <div class="students">
            <p>کارآموزان دوره ها</p>
            <div class="d-flex">
                <div class="student-profiles">
                    <img src="{{ asset('images/student1.jpg') }}" class="student-profile">
                    <img src="{{ asset('images/student2.jpg') }}" class="student-profile">
                    <img src="{{ asset('images/student3.jpg') }}" class="student-profile">
                    <img src="{{ asset('images/student4.jpg') }}" class="student-profile">
                    <img src="{{ asset('images/student5.jpg') }}" class="student-profile">
                </div>
                <span style="direction: ltr;">+20</span>
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
            <div class="col-md-3 col-xs-6 col-6">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/git.png') }}" alt="Git">
                        </div>
                        <p>کنترل پروژه</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-6 col-6">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/programming.png') }}" alt="Git">
                        </div>
                        <p>برنامه نویسی</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-6 col-6">
                <div class="skill-box px-0 mx-0 row justify-content-center align-items-center">
                    <div class="d-flex justify-content-center align-items-center flex-column">
                        <div class="icon-box">
                            <img src="{{ asset('images/server.png') }}" alt="Git">
                        </div>
                        <p>لینوکس</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-xs-6 col-6">
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
            <h2 class="title">دوره های آموزشی من</h2>
            <div class="breaker"></div>
            <p class="tutorials-section-description">
                من توی دوره های آموزشیم سعی میکنم مفاهیم و مهارت ها رو به صورت واضح و ساده بیان کنم. تا جای ممکن هم
                پروژه محور پیش میریم که براتون کاربردی باشه.
            </p>
            <div class="btn btn-sm button-primary d-inline-block">مشاهده همه</div>
        </div>
        <div class="col-xl-6 col-12 splide tutorials-right" role="group" aria-label="اسلایدر دوره ها">
            <div class="splide__track">
                <ul class="splide__list">
                    <li class="card tutorial splide__slide">
                        <a href="#">
                            <img src="{{ asset('images/web-browsing.png') }}" class="thumbnail" alt="وبگردی حرفه ای">
                            <h3 class="post-title">وبگردی حرفه ای</h3>
                        </a>
                        <div class="d-flex post-meta">
                            <a href="#" class="btn btn-sm button-primary">مشاهده</a>
                            <div class="d-flex align-items-center students-count">
                                <i class="fas fa-user-circle"></i>
                                دانشجویان: ۲۰
                            </div>
                        </div>
                    </li>
                    <li class="card tutorial splide__slide">
                        <a href="#">
                            <img src="{{ asset('images/html5.jpg') }}" class="thumbnail" alt="دوره آموزش کامل HTML5">
                            <h3 class="post-title">آموزش کامل HTML5</h3>
                        </a>
                        <div class="d-flex post-meta">
                            <a href="#" class="btn btn-sm button-primary">مشاهده</a>
                            <div class="d-flex align-items-center students-count">
                                <i class="fas fa-user-circle"></i>
                                دانشجویان: ۰
                            </div>
                        </div>
                    </li>
                    <li class="card tutorial splide__slide">
                        <a href="#">
                            <img src="{{ asset('images/css3.jpg') }}" class="thumbnail" alt="دوره آموزش کامل CSS3">
                            <h3 class="post-title">آموزش کامل طراحی وب با CSS3</h3>
                        </a>
                        <div class="d-flex post-meta">
                            <a href="#" class="btn btn-sm button-primary">مشاهده</a>
                            <div class="d-flex align-items-center students-count">
                                <i class="fas fa-user-circle"></i>
                                دانشجویان: ۰
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section id="portfolios">
        <h2 class="title text-center">جدیدترین نمونه کار ها</h2>
        <div class="breaker mx-auto"></div>
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                <a href="#">
                    <img src="https://placehold.co/600x400/png" alt="Project 1">
                </a>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                <a href="#">
                    <img src="https://placehold.co/600x400/png" alt="Project 2">
                </a>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                <a href="#">
                    <img src="https://placehold.co/600x400/png" alt="Project 2">
                </a>
            </div>
            <div class="col-xl-6 col-md-6 col-12">
                <a href="#">
                    <img src="https://placehold.co/600x400/png" alt="Project 2">
                </a>
            </div>
            <div class="col-xl-6 col-md-6 col-12">
                <a href="#">
                    <img src="https://placehold.co/600x400/png" alt="Project 2">
                </a>
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
                    <li class="card blog splide__slide">
                        <img src="{{ asset('images/tutorial.png') }}" class="thumbnail" alt="وبگردی حرفه ای">
                        <h3 class="post-title">وبگردی حرفه ای</h3>
                        <div class="d-flex post-meta">
                            <a href="#" class="btn btn-sm button-primary">مشاهده</a>
                        </div>
                    </li>
                    <li class="card blog splide__slide">
                        <img src="{{ asset('images/tutorial.png') }}" class="thumbnail" alt="وبگردی حرفه ای">
                        <h3 class="post-title">وبگردی حرفه ای</h3>
                        <div class="d-flex post-meta">
                            <a href="#" class="btn btn-sm button-primary">مشاهده</a>
                        </div>
                    </li>
                    <li class="card blog splide__slide">
                        <img src="{{ asset('images/tutorial.png') }}" class="thumbnail" alt="وبگردی حرفه ای">
                        <h3 class="post-title">وبگردی حرفه ای</h3>
                    </li>
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
                            <div class="screen-header-button close"></div>
                            <div class="screen-header-button maximize"></div>
                            <div class="screen-header-button minimize"></div>
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
                                <button class="btn btn-sm btn-outline-secondary text-white btn-w-icon">
                                    <i class="fa fa-send ml-1"></i>
                                    ثبت فرم
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</main>
@stop()