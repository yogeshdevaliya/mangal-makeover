@extends('layouts.auth')

@section('content')
<div class="container wh-100">
	<div class="row wh-100 align-items-center justify-content-center">
		<div class="col-md-6">
			<div class="auth-card">
				<div class="auth-logo"><label>Makeover <br/> Studio</label></div>
				<div class="auth-card-body">
					<form method="POST" action="{{ route('register') }}">
						@csrf

						<input id="name" type="text" class="form-input form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" placeholder="Name/Username" required autofocus>
						@if ($errors->has('name'))
							<span class="invalid-feedback" role="alert">
								<strong>{{ $errors->first('name') }}</strong>
							</span>
						@endif
						<div class="clearfix"></div><br />
						<input id="email" type="email" class="form-input form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Your Email Address" required>
						@if ($errors->has('email'))
							<span class="invalid-feedback" role="alert">
								<strong>{{ $errors->first('email') }}</strong>
							</span>
						@endif
						<div class="clearfix"></div><br />
						<input id="password" type="password" class="form-input form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Your Password" required>
						@if ($errors->has('password'))
							<span class="invalid-feedback" role="alert">
								<strong>{{ $errors->first('password') }}</strong>
							</span>
						@endif
						<div class="clearfix"></div><br />
						<input id="password-confirm" type="password" placeholder="Confirm Password" class="form-input form-control" name="password_confirmation" required>
						<div class="clearfix"></div><br />
						<button type="submit" class="form-btn">
							{{ __('Register') }}
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
