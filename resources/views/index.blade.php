@extends('layouts.app')

@section('title', setting("tebex.shop.title", trans("tebex::messages.shop")))

@section('content')

@php
$i = 0;
$y = 0;
@endphp

<div class="row">
    <div class="mb-3">
        <h1>{{ setting("tebex.shop.title", trans("tebex::messages.shop") )}}</h1>

        @if(setting("tebex.shop.subtitle"))
            <h4>{{ setting("tebex.shop.subtitle") }}</h4>
        @endif 
    </div>
    <div class="col-lg-3">
        <div class="list-group mb-3" role="tablist">
            @foreach($categories as $categorie)
            <a class="list-group-item @if($i == 0) active @endif" data-bs-toggle="tab" data-bs-target="#pill-{{ $categorie->id }}" type="button" role="tab" aria-controls="pill-{{ $categorie->id }}" aria-selected="true">{{ $categorie->name }}</a>
            @php
            $i++
            @endphp
            @endforeach
        </div>
    </div>

    <div class="col-lg-9 tab-content">
        @forelse($categories as $categorie)
            <div class="tab-pane fade show @if($y == 0) active @endif" id="pill-{{ $categorie->id }}" role="tabpanel">
                <div class="row gy-4">
                    @forelse($categorie->packages as $package)
                    <div class="col-md-4">
                        <div class="card h-100">
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

                                <button class="btn btn-primary btn-block" onclick="openProductModal(`{{ $package->id }}`)">
                                    {{ trans('tebex::messages.packages.buy') }}
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
            @php
                $y++
            @endphp
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

@guest
    <script>
        var pseudo = ""
    </script>
@else
    <script>
        var pseudo = "{{ Auth::user()->name }}"
    </script>
@endguest

<script>
    var api = "{{ route('tebex.api.buy') }}";

    function openInNewTab(url) {
        window.open(url, '_blank').focus();
    }

    function openProductModal(product_id) {

        Swal.fire({
            title: "{{ trans('tebex::messages.modal.mc_pseudo') }}",
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                min: 3,
            },
            inputPlaceholder: 'Steve',
            showCancelButton: true,
            reverseButtons: true,
            inputValue: pseudo,
            confirmButtonText: "{{ trans('tebex::messages.packages.buy') }}",
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                if (value.length < 3) {
                    return "{{ trans('tebex::messages.modal.bad_username') }}"
                }
            },
            preConfirm: (username) => {
                return fetch(api, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            username: username,
                            package_id: product_id
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Oups ! : ${error}`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                openInNewTab(result.value.checkout_url);
            }
        });
        
    }
</script>
@endpush