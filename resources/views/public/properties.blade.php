<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties for Rent | DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="/images/logo.png">
    <style>
        body {
            background: #f6f7fb;
            color: #172033;
            font-family: 'Segoe UI', sans-serif;
        }
        .public-nav {
            background: #101827;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            color: #fff;
        }
        .brand-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
        }
        .hero {
            background:
                linear-gradient(90deg, rgba(15, 23, 42, 0.86), rgba(15, 23, 42, 0.5)),
                url('/images/login.jpg') center/cover;
            color: #fff;
            min-height: 360px;
            display: flex;
            align-items: center;
        }
        .hero h1 {
            font-size: clamp(2rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.05;
        }
        .filter-band {
            margin-top: -42px;
            position: relative;
            z-index: 2;
        }
        .property-card {
            background: #fff;
            border: 1px solid #e5e7ef;
            border-radius: 8px;
            min-height: 100%;
            box-shadow: 0 8px 24px rgba(17, 24, 39, 0.06);
        }
        .property-media {
            height: 150px;
            background: #101827;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }
        .property-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .price {
            color: #e94560;
            font-size: 1.45rem;
            font-weight: 800;
        }
        .request-panel {
            background: #fff;
            border: 1px solid #e5e7ef;
            border-radius: 8px;
            position: sticky;
            top: 18px;
            box-shadow: 0 8px 24px rgba(17, 24, 39, 0.06);
        }
        .btn-dream {
            background: #e94560;
            border-color: #e94560;
            color: #fff;
        }
        .btn-dream:hover {
            background: #d73652;
            border-color: #d73652;
            color: #fff;
        }
        @media (max-width: 991px) {
            .request-panel {
                position: static;
            }
        }
    </style>
</head>
<body>
    <nav class="public-nav py-3">
        <div class="container d-flex align-items-center">
            <a href="{{ route('public.properties') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white fw-bold">
                <img src="/images/logo.png" alt="DreamHome logo" class="brand-logo">
                DreamHome
            </a>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="col-lg-7">
                <h1>Find a rental home that fits your next move.</h1>
                <p class="lead mt-3 mb-0">Browse available DreamHome properties, choose the one you like, and send a request for approval by our team.</p>
            </div>
        </div>
    </section>

    <main class="container pb-5">
        <section class="filter-band mb-4">
            <form method="GET" action="{{ route('public.properties') }}" class="bg-white border rounded-3 shadow-sm p-3 row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control">
                        <option value="">Any type</option>
                        @foreach($types as $type)
                            <option value="{{ $type->type }}" {{ request('type') === $type->type ? 'selected' : '' }}>{{ $type->type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ request('city') }}" placeholder="Search city">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Min rooms</label>
                    <input type="number" name="rooms" class="form-control" min="1" value="{{ request('rooms') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Max rent</label>
                    <input type="number" name="max_rent" class="form-control" min="0" step="0.01" value="{{ request('max_rent') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>
        </section>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                Please check the request form and try again.
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h4 mb-0">Available Properties</h2>
                    <span class="text-muted">{{ count($properties) }} found</span>
                </div>
                <div class="row g-3">
                    @forelse($properties as $property)
                        @php
                            $houseImages = ['house1.jpg', 'house2.png', 'house3.jpg'];
                            $flatImages = ['flat1.jpg', 'flat2.jpg'];
                            $propertyType = strtolower($property->type ?? '');

                            if ($propertyType === 'studio') {
                                $propertyImage = 'studio.jpg';
                            } elseif ($propertyType === 'flat') {
                                $propertyImage = $flatImages[$loop->index % count($flatImages)];
                            } else {
                                $propertyImage = $houseImages[$loop->index % count($houseImages)];
                            }
                        @endphp
                        <div class="col-md-6">
                            <article class="property-card">
                                <div class="property-media">
                                    <img src="{{ asset('images/' . $propertyImage) }}" alt="{{ $property->type ?: 'Property' }} at {{ $property->street }}">
                                </div>
                                <div class="p-3">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <h3 class="h5 mb-1">{{ $property->street }}</h3>
                                            <div class="text-muted">{{ $property->area ? $property->area . ', ' : '' }}{{ $property->city }}</div>
                                        </div>
                                        <span class="badge bg-success align-self-start">Available</span>
                                    </div>
                                    <div class="d-flex gap-3 text-muted my-3">
                                        <span><i class="bi bi-building"></i> {{ $property->type ?: 'Home' }}</span>
                                        <span><i class="bi bi-door-open"></i> {{ $property->rooms ?: 'N/A' }} rooms</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="price">GBP {{ number_format($property->rent, 2) }}</div>
                                        <button
                                            type="button"
                                            class="btn btn-dream btn-sm"
                                            data-property="{{ $property->property_no }}"
                                            data-type="{{ $property->type }}"
                                            data-rent="{{ $property->rent }}"
                                            onclick="selectProperty(this)">
                                            <i class="bi bi-send"></i> Request
                                        </button>
                                    </div>
                                    <div class="mt-2 small text-muted">Property {{ $property->property_no }} | Branch {{ $property->branch_city ?: 'N/A' }}</div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="bg-white border rounded-3 p-5 text-center">
                                <h3 class="h5">No properties match your search.</h3>
                                <p class="text-muted mb-0">Try adjusting your filters or check back later.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-4">
                <aside class="request-panel p-4" id="request-form">
                    <h2 class="h4 mb-1">Request Approval</h2>
                    <p class="text-muted">Send your details and selected property to DreamHome.</p>

                    <form action="{{ route('client-requests.store') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Selected Property</label>
                            <select name="property_no" id="property_no" class="form-control @error('property_no') is-invalid @enderror" required>
                                <option value="">Choose a property</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->property_no }}" {{ old('property_no') === $property->property_no ? 'selected' : '' }}>
                                        {{ $property->property_no }} - {{ $property->street }}, {{ $property->city }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="f_name" value="{{ old('f_name') }}" class="form-control @error('f_name') is-invalid @enderror" required maxlength="30">
                            @error('f_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="l_name" value="{{ old('l_name') }}" class="form-control @error('l_name') is-invalid @enderror" required maxlength="40">
                            @error('l_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Phone</label>
                            <input type="text" name="tel_no" value="{{ old('tel_no') }}" class="form-control @error('tel_no') is-invalid @enderror" required maxlength="20">
                            @error('tel_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" maxlength="120">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Preferred Viewing Date</label>
                            <input type="date" name="preferred_view_date" value="{{ old('preferred_view_date') }}" class="form-control @error('preferred_view_date') is-invalid @enderror">
                            @error('preferred_view_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea name="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-dream">
                                <i class="bi bi-envelope-check"></i> Send Request
                            </button>
                        </div>
                    </form>
                </aside>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectProperty(button) {
            const propertySelect = document.getElementById('property_no');

            propertySelect.value = button.dataset.property;

            document.getElementById('request-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    </script>
</body>
</html>
