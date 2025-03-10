<div class="row g-3">
    <div class="mb-3 col-md-6">
        <label class="form-label" for="merchantIdInput">{{ trans('paytm::messages.merchant-id') }}</label>
        <input type="text" class="form-control @error('merchant-id') is-invalid @enderror" id="merchantIdInput" name="merchant-id" value="{{ old('merchant-id', $gateway->data['merchant-id'] ?? '') }}" required>

        @error('merchant-id')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="merchantKeyInput">{{ trans('paytm::messages.merchant-key') }}</label>
        <input type="text" class="form-control @error('merchant-key') is-invalid @enderror" id="merchantKeyInput" name="merchant-key" value="{{ old('merchant-key', $gateway->data['merchant-key'] ?? '') }}" required>

        @error('merchant-key')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="websiteInput">{{ trans('paytm::messages.website') }}</label>
        <input type="text" class="form-control @error('website') is-invalid @enderror" id="websiteInput" name="website" value="{{ old('website', $gateway->data['website'] ?? 'DEFAULT') }}" required>

        @error('website')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="industryTypeInput">{{ trans('paytm::messages.industry-type') }}</label>
        <input type="text" class="form-control @error('industry-type') is-invalid @enderror" id="industryTypeInput" name="industry-type" value="{{ old('industry-type', $gateway->data['industry-type'] ?? 'Retail') }}" required>

        @error('industry-type')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label" for="channelIdInput">{{ trans('paytm::messages.channel-id') }}</label>
        <input type="text" class="form-control @error('channel-id') is-invalid @enderror" id="channelIdInput" name="channel-id" value="{{ old('channel-id', $gateway->data['channel-id'] ?? 'WEB') }}" required>

        @error('channel-id')
        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="alert alert-info">
    <p>
        <i class="bi bi-info-circle"></i>
        @lang('paytm::messages.setup', [
            'callback' => '<code>'.route('shop.payments.notification', 'paytm').'</code>',
        ])
    </p>

    <a class="btn btn-primary mb-3" target="_blank" href="https://dashboard.paytm.com/" role="button">
        <i class="bi bi-box-arrow-in-right"></i> {{ trans('paytm::messages.dashboard') }}
    </a>
</div>
