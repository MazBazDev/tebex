@extends('admin.layouts.admin')

@section('title', trans('tebex::admin.settings.title'))

@section('content')
<div class="card shadow mb-4">
    <div class="card-body">

        <form action="{{ route('tebex.admin.index') }}" method="POST">
            @csrf

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="tebex_active" name="tebex_active" data-bs-toggle="collapse" @checked(tebexMode())>
                    <label class="form-check-label" for="tebex_active">{{ trans('tebex::admin.settings.tebex_active') }}</label>
                </div>
                <small class="form-text">{{ trans('tebex::admin.settings.tebex_mode_info') }}</small>
            </div>

            <div class="mb-3">
                <label class="form-label" for="tebex_currency">{{ trans('tebex::messages.fields.currency') }}</label>

                <select class="form-select @error('tebex_currency') is-invalid @enderror" id="tebex_currency" name="tebex_currency">
                    @foreach($tebex_currencies as $code => $name)
                        <option value="{{ $code }}" @selected($tebex_current_currency === $code)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="tebex_key">{{ trans('tebex::admin.settings.tebex_key') }}</label>

                <div class="input-group @error('tebex_key') has-validation @enderror" v-scope="{toggle: false}">
                    <input :type="toggle ? 'text' : 'password'" type="text" min="0" class="form-control @error('tebex_key') is-invalid @enderror" id="tebex_key" name="tebex_key" value="{{ old('tebex_key', $tebex_key) }}">
                    <button @click="toggle = !toggle" type="button" class="btn btn-outline-primary">
                        <i class="bi" :class="toggle ? 'bi-eye' : 'bi-eye-slash'"></i>
                    </button>
                    @error('tebex_key')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <small class="form-text">{{ trans('tebex::admin.settings.tebex_key_info') }} <a target='_blank' href="https://docs.tebex.io/store/faq">https://docs.tebex.io/store/faq</a></small>
            </div> 
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
            </button>
        </form>

    </div>
</div>
@endsection