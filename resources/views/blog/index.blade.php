@php use Morilog\Jalali\Jalalian; @endphp
@section('title', 'وبلاگ')
@extends('layouts.app')
@section('content')
    <main class="container">
        <div class="col-md-8 offset-md-2 mb-5">
            <form action="{{ url('blog') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control form-control-lg ms-sm-2"
                       placeholder="مثلا: زبان های برنامه نویسی محبوب 2023"
                       value="{{ strlen($searchQuery) > 0 ? $searchQuery : '' }}">
                <button type="submit" class="btn btn-primary my-2 my-sm-0">جست و جو</button>
            </form>
        </div>
        @if(strlen($searchQuery) === 0)
            <div class="p-4 p-md-5 m-4 rounded text-center text-bg-dark">
                <div class="col-12 px-0">
                    <h1 class="display-6 mb-4 fw-bold text-primary">
                        به روز ترین مقالات به ساده‌ترین دست خط
                    </h1>
                    <p class="lead my-3">
                        لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است،
                        چاپگرها و متون بلکه
                        روزنامه و مجله در ستون و سطرآنچنان که لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و
                        کاربردهای
                        متنوع با هدف بهبود
                        ابزارهای کاربردی می باشد، کتابهای زیادی در شصت و سه درصد گذشته حال و آینده، شناخت فراوان جامعه و
                        متخصصان را می طلبد، تا
                        با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی، و فرهنگ پیشرو در
                        زبان
                        فارسی ایجاد کرد، در
                        این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها، و شرایط سخت تایپ به پایان
                        رسد و
                        زمان مورد نیاز شامل
                        حروفچینی دستاوردهای اصلی، و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد استفاده قرار
                        گیرد.
                    </p>
                    <div class="d-inline-flex gap-2 my-3">
                        <button class="d-inline-flex align-items-center btn btn-primary btn-lg px-4 rounded-pill"
                                type="button">
                            <i class="fa fa-solid fa-arrow-left me-1"></i>
                            مقالات پربازدید
                        </button>
                        <button class="btn btn-outline-secondary btn-lg px-4 rounded-pill" type="button">
                            اخبار تکنولوژی
                        </button>
                    </div>
                </div>
            </div>

            <h2 class="text-light ms-4 mt-5 mb-4">✨مقالات ویژه</h2>
            <section class="row mb-2">
                @foreach($pinnedPosts as $post)
                    <div class="col-md-6">
                        <div class="card animated-card row g-0 px-0 border rounded overflow-hidden flex-md-row mb-4 position-relative bg-light">
                            <div class="col p-4 d-flex flex-column position-static">
                                @if($post->categories?->first())
                                    <strong class="d-inline-block badge text-bg-primary w-fit-content">
                                        {{ $post->categories?->first()?->title }}
                                    </strong>
                                @endif
                                <a href="{{ $post->link }}">
                                    <h3 class="post-title mt-2 mb-1">{{ $post->title }}</h3>
                                </a>
                                <div class="mt-2 text-muted">
                                    {{ Jalalian::fromDateTime($post->created_at)->format('d F Y') }}
                                </div>
                            </div>
                            <div class="col-6 d-none d-lg-block">
                                <a href="{{ $post->link }}">
                                    <img style="max-height: 250px; width: 100%; aspect-ratio: 1 / 1; object-fit: cover;"
                                         src="{{ asset('storage/upload/' . $post->thumbnail) }}"
                                         alt="{{ $post->title }}">
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        <h2 class="text-light ms-4 mt-5 mb-4">
            @if(strlen($searchQuery) === 0)
                ☕ جدیدترین مقالات
            @else
                🔍 نتیجه جست و جو
            @endif
        </h2>
        <div class="row">
            @forelse($posts as $post)
                <div class="col-md-4 mb-4">
                    <div class="card post animated-card">
                        <a href="{{ $post->link }}">
                            <img src="{{ asset('storage/upload/' . $post->thumbnail) }}"
                                 class="card-img-top" alt="{{ $post->title }}">
                        </a>
                        <div class="card-body">
                            <div class="small text-muted">
                                {{ Jalalian::fromDateTime($post->created_at)->format('d F Y') }}
                            </div>
                            <a href="{{ $post->link }}">
                                <h3 class="post-title">{{ $post->title }}</h3>
                            </a>
                            <p class="card-text">{{ $post->excerpt }}</p>
                            <a href="{{ $post->link }}" class="btn text-light btn-primary btn-w-icon">
                                <i class="fa fa-solid fa-eye me-1"></i>
                                ادامه مطلب
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 mb-4">
                    <div class="card p-5 text-center">
                        <img src="{{ asset('images/empty.svg') }}" alt="نتیجه ای یافت نشد" height="200px">
                        <p class="h5 fw-normal my-3">
                            مقاله ای برای نمایش یافت نشد
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ url('/blog') }}" class="btn text-light btn-primary">
                                جست و جو رو بیخیال 😅
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-w-icon">
                                <i class="fa fa-solid fa-arrow-right me-1"></i>
                                برگرد صفحه اصلی
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </main>
@endsection