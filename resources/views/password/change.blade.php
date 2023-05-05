@extends('layouts.main')

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h4>
                <strong>Change Password</strong>
            </h4>

            <div class="clearfix"></div><br />
            @if (session('res'))
                <div class="alert alert-{{ session('res')['status'] }}" data-dismiss="alert">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong>{{ session('res')['message'] }}</strong>
                </div>
            @endif
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- Horizontal Form -->
                    <div class="box box-warning">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <!-- form start -->
                            <form method="POST" action="{{ url('admin/change/password') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label for="old_password"
                                        class="col-md-2 col-form-label"><strong>{{ __('Old Password') }}</strong><span
                                            class="required clr-red">*</span></label>
                                    <div class="col-md-6">
                                        <input type="password" name="old_password" class="form-control" id="old_password"
                                            placeholder="Enter Old Password" required>
                                        @if ($errors->has('old_password'))
                                            <span class="invalid-feedback required-error" role="alert">
                                                <strong>{{ $errors->first('old_password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password"
                                        class="col-md-2 col-form-label"><strong>{{ __('Password') }}</strong><span
                                            class="required clr-red">*</span></label>
                                    <div class="col-md-6">
                                        <input type="password" name="password" class="form-control" id="password"
                                            placeholder="Enter Password" required>
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback required-error" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password_confirmation"
                                        class="col-md-2 col-form-label"><strong>{{ __('Confirm Password') }}</strong><span
                                            class="required clr-red">*</span></label>
                                    <div class="col-md-6">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            id="password_confirmation" placeholder="Enter Confirm Password" required>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-8 text-right">
                                        <button type="submit" class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                    </div>
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
@endsection
@section('scripts')
    <script type="text/javascript"></script>
@endsection
