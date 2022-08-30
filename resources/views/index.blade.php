@extends('layouts.app')

@section('title', setting("tebex.shop.title", trans("tebex::messages.shop")))

@section('content')

@php
    $i = 0;
    $y = 0;
@endphp

<div class="row">
    <div class="mb-3">
        @if(setting("tebex.shop.title"))
            <h1>{{ setting("tebex.shop.title")}}</h1>
        @endif
        @if(setting("tebex.shop.subtitle"))
            <h4>{{ setting("tebex.shop.subtitle") }}</h4>
        @endif
    </div>
    <div class="col-lg-3">
        <div class="list-group mb-3" role="tablist">
            @if(setting('tebex.shop.home', true))
                <a class="list-group-item list-group-item-action @if($i == 0) active @endif" data-bs-toggle="tab" data-bs-target="#pill-home" type="button" role="tab" aria-controls="pill-home" aria-selected="true">
                    <i class="bi bi-house-door"></i> {{ trans('tebex::messages.home.home') }}
                </a>
                @php $i++ @endphp
            @endif
            @foreach($categories as $categorie)
                <a class="list-group-item list-group-item-action @if($i == 0) active @endif" data-bs-toggle="tab" data-bs-target="#pill-{{ $categorie->id }}" type="button" role="tab" aria-controls="pill-{{ $categorie->id }}" aria-selected="true">
                    {{ $categorie->name }}
                </a>
                @foreach($categorie->subcategories as $subCategorie)
                    <a class="list-group-item list-group-item-action" data-bs-toggle="tab" data-bs-target="#pill-{{ $subCategorie->id }}" type="button" role="tab" aria-controls="pill-{{ $subCategorie->id }}" aria-selected="true">
                        <i class="bi bi-arrow-return-right"></i> 
                        {{ $subCategorie->name }}
                    </a>
                @endforeach
                @php $i++ @endphp
            @endforeach
        </div>
    </div>
    <div class="col-lg-9 tab-content">
        @if(setting('tebex.shop.home', true))
            <div class="tab-pane fade @if($y == 0) active show @endif" id="pill-home" role="tabpanel">
                <div class="card card-body">
                    {!! setting('tebex.shop.home.message', trans('tebex::messages.home.placeholder')) !!}
                </div>
            </div>
            @php $y++ @endphp
        @endif
        
        @forelse($categories as $categorie)
            <div class="tab-pane fade @if($y == 0) active show @endif" id="pill-{{ $categorie->id }}" role="tabpanel">
                <div class="row gy-4">
                    @forelse($categorie->packages as $package)
                        <div class="col-md-4">
                            <div class="card h-100 py-2" onclick="showModal('{{ $package->name }}', '{!! $package->description !!}', '{{ $package->id }}', '{{ $package->price->discounted ? $package->price->discounted . tebex_currency_symbol() : $package->price->normal . tebex_currency_symbol() }}')">
                                @if($package->image)
                                    <img class="card-img-top" draggable="false" src="{{ $package->image }}" alt="{{ $package->name }}">
                                @endif

                                <div class="card-body">
                                    <h4 class="card-title">{{ $package->name }}</h4>
                                    <h5 class="card-subtitle mb-3">
                                        @if($package->price->discounted)
                                            <del class="small">{{ $package->price->normal . tebex_currency_symbol() }}</del>
                                            {{ $package->price->discounted . tebex_currency_symbol() }}
                                        @else
                                            {{ $package->price->normal . tebex_currency_symbol() }}
                                        @endif
                                    </h5>
                                    <button class="btn btn-primary btn-block w-100">
                                        <i class="bi bi-eye"></i>
                                        {{ trans('tebex::messages.packages.show') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col">
                            <div class="alert alert-warning" role="alert">
                                {{ trans('tebex::messages.categories.empty') }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            @foreach($categorie->subcategories as $categorie)
                <div class="tab-pane fade" id="pill-{{ $categorie->id }}" role="tabpanel">
                    <div class="row gy-4">
                        @forelse($categorie->packages as $package)
                            <div class="col-md-4">
                                <div class="card h-100 py-2" onclick="showModal('{{ $package->name }}', '{{ $package->description }}', '{{ $package->id }}', '{{ $package->price->discounted ? $package->price->discounted . tebex_currency_symbol() : $package->price->normal . tebex_currency_symbol() }}')">
                                    @if($package->image)
                                        <img class="card-img-top" draggable="false" src="{{ $package->image }}" alt="{{ $package->name }}">
                                    @endif

                                    <div class="card-body">
                                        <h4 class="card-title">{{ $package->name }}</h4>
                                        <h5 class="card-subtitle mb-3">
                                            @if($package->price->discounted)
                                                <del class="small">{{ $package->price->normal . tebex_currency_symbol() }}</del>
                                                {{ $package->price->discounted . tebex_currency_symbol() }}
                                            @else
                                                {{ $package->price->normal . tebex_currency_symbol() }}
                                            @endif
                                        </h5>
                                        <button class="btn btn-primary btn-block w-100">
                                            <i class="bi bi-eye"></i>
                                            {{ trans('tebex::messages.packages.show') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col">
                                <div class="alert alert-warning" role="alert">
                                    {{ trans('tebex::messages.categories.empty') }}
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
            
            @php $y++ @endphp
        @empty
            <div class="col">
                <div class="alert alert-warning" role="alert">
                    {{ trans('tebex::messages.categories.empty') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ plugin_asset('tebex', 'js/script.js') }}"></script>
    <script>
        var api = "{{ route('tebex.api.buy') }}";
        let title = "{{ trans('tebex::messages.modal.mc_pseudo') }}";
        let buy = "{{ trans('tebex::messages.packages.buy') }}";
        let errorUser = "{{ trans('tebex::messages.modal.bad_username') }}";
        let cancel = "{{ trans('tebex::messages.packages.cancel') }}";
    </script>
    @guest
        <script>
            var pseudo = ""
        </script>
    @else
        <script>
            var pseudo = "{{ Auth::user()->name }}"
        </script>
    @endguest 
@endpush