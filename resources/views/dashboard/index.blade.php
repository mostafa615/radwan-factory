@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content-header">

            <h1>@lang('site.profile')</h1>

            <!-- <ol>
                <li><i class="fa fa-dashboard"></i>@lang('site.profile')</li>
                <li class="active"><i class="fa fa-dashboard"></i>@lang('site.users')</li>
            </ol> -->
            <ol class="breadcrumb">
                <li class="active"><i class="fa fa-dashboard"></i>@lang('site.dashboard')</li>
                <li><a href="{{route('dashboard.index')}}">@lang('site.profile')</a></li>
            </ol>

        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-9">
                        <div class="nav-tabs-custom">
                          <ul class="nav nav-tabs">
                            <li class="active"><a href="#dash" data-toggle="tab">@lang('site.dashboard')</a></li>
                            {{-- <li><a href="#activity" data-toggle="tab">@lang('site.activity')</a></li> --}}
                            <li><a href="#loginHistory" data-toggle="tab">@lang('site.login_proccess')</a></li>
                            <li><a href="#settings" data-toggle="tab">@lang('site.settings')</a></li>
                            <li><a href="#password" data-toggle="tab">@lang('site.change_password')</a></li>
                            <li><a href="#phone" data-toggle="tab">@lang('site.change_phone')</a></li>
                          </ul>
                          <div class="tab-content">

                            <div class="active tab-pane" id="dash">
                                <div class="row">

                                    {{-- subjects--}}
                                    <div class="col-lg-4 col-xs-12">
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                                {{-- <h3>
                                                    @if (auth()->user()->type == 'doctor')
                                                     {{auth()->user()->toDoctor()->subjects->count()}}
                                                    @endif
                                                    @if (auth()->user()->type == 'student')
                                                     {{auth()->user()->toStudent()->stdSbjs->count()}}
                                                    @endif
                                                </h3> --}}
                                                <a href="{{route('dashboard.machines.index')}}">@lang('site.machines')</a>
                                            </div>
                                            <div class="icon">
                                                {{-- <i class="ion ion-bag"></i> --}}
                                                <i class="fa fa-graduation-cap"></i>
                                            </div>
                                            <a href="{{route('dashboard.machines.index')}}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>

                                    {{--lessons--}}
                                    <div class="col-lg-4 col-xs-12">
                                        <div class="small-box bg-green">
                                            <div class="inner">
                                                {{-- <h3>
                                                    @if (auth()->user()->type == 'doctor')
                                                     {{auth()->user()->toDoctor()->lessons->count()}}
                                                    @endif
                                                    @if (auth()->user()->type == 'student')
                                                    {{--   {{auth()->user()->toStudent()->stdSbjs()->subjects()->lessons->count()}} --}
                                                    @endif
                                                </h3> --}}

                                                <a href="{{route('dashboard.supplies.index')}}">@lang('site.operation_supplies')</a>
                                            </div>
                                            <div class="icon">
                                                {{-- <i class="ion ion-stats-bars"></i> --}}
                                                <i class="fa fa-list-alt"></i>
                                            </div>
                                            <a href="{{route('dashboard.supplies.index')}}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>

                                    {{--assginments--}}
                                    <div class="col-lg-4 col-xs-12">
                                        <div class="small-box bg-red">
                                            <div class="inner">
                                                {{-- <h3>
                                                    @if (auth()->user()->type == 'doctor')
                                                     {{auth()->user()->toDoctor()->assignments->count()}}
                                                    @endif
                                                </h3> --}}
                                                <a href="{{route('dashboard.operation_orders.index')}}">@lang('site.operation_orders')</a>
                                            </div>
                                            <div class="icon">
                                                <i class="fa fa-user"></i>
                                            </div>
                                            <a href="{{route('dashboard.operation_orders.index')}}" class="small-box-footer">@lang('site.read') <i class="fa fa-arrow-circle-right"></i></a>
                                        </div>
                                    </div>


                                </div><!-- end of row -->

                            </div>

                            <div class="tab-pane" id="activity">
                                <!-- The timeline -->
                                <ul class="timeline timeline-inverse">
                                  <!-- timeline time label -->
                                  <li class="time-label">
                                        <span class="bg-red">
                                          2020-08-09
                                        </span>
                                  </li>
                                  <!-- /.timeline-label -->
                                  <!-- timeline item -->
                                  <li>
                                    <i class="fa fa-envelope bg-blue"></i>

                                    <div class="timeline-item">
                                      <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                                      <h3 class="timeline-header"><a href="#">Notification 1</a> </h3>

                                      <div class="timeline-body">
                                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                        weebly ning heekya handango imeem plugg dopplr jibjab, movity.
                                      </div>

                                    </div>
                                  </li>
                                  <!-- END timeline item -->
                                  <!-- timeline item -->
                                  <li>
                                    <i class="fa fa-user bg-aqua"></i>

                                    <div class="timeline-item">
                                      <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>

                                      <h3 class="timeline-header"><a href="#">Notification 2 </a> </h3>

                                      <div class="timeline-body">
                                        Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                        weebly ning heekya handango imeem plugg dopplr jibjab, movity.
                                      </div>

                                    </div>
                                  </li>
                                  <!-- END timeline item -->
                                  <!-- timeline item -->
                                  <li>
                                    <i class="fa fa-comments bg-yellow"></i>

                                    <div class="timeline-item">
                                      <span class="time"><i class="fa fa-clock-o"></i> 08:05</span>

                                      <h3 class="timeline-header"><a href="#">Notification 3</a> </h3>

                                      <div class="timeline-body">
                                        Take me to your leader!
                                        Switzerland is small and neutral!
                                        We are more like Germany, ambitious and misunderstood!
                                      </div>

                                    </div>
                                  </li>
                                  <!-- END timeline item -->
                                  <!-- timeline time label -->
                                  <li class="time-label">
                                        <span class="bg-green">
                                          2020-07-5
                                        </span>
                                  </li>
                                  <!-- /.timeline-label -->
                                  <!-- timeline item -->
                                  <li>
                                    <i class="fa fa-camera bg-purple"></i>

                                    <div class="timeline-item">
                                      <span class="time"><i class="fa fa-clock-o"></i> 10:30</span>

                                      <h3 class="timeline-header"><a href="#">Notification 4</a> </h3>

                                      <div class="timeline-body">
                                          Take me to your leader!
                                          Switzerland is small and neutral!
                                          We are more like Germany, ambitious and misunderstood!
                                      </div>
                                    </div>
                                  </li>
                                  <!-- END timeline item -->
                                  <li>
                                    <i class="fa fa-clock-o bg-gray"></i>
                                  </li>
                                </ul>
                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane table-responsive" id="loginHistory">
                                <!-- The timeline -->
                                <table class="table table-border" id="table" >
                                    <thead>
                                        <tr>
                                            <th>{{ __('datetime') }}</th>
                                            <th>{{ __('ip') }}</th>
                                            <th>{{ __('device info') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($loginHistories))
                                        @foreach($loginHistories as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->ip }}</td>
                                            <td>{!! $item->phone_details !!}</td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.tab-pane -->


                            <div class="tab-pane" id="settings">

                                <div class="box">
                                      <div class="box-body">
                                        <form action="" method="POST" class="form-horizontal" id="chnameform">
                                            @csrf
                                            {{-- {{method_field('')}} --}}

                                            <input type="hidden" name="id_hid" id="id_hid" value="{{auth()->user()->id}}">
                                            <input type="hidden" name="action" id="action" value="Edit">

                                            <div class="form-group">
                                                <label>@lang('site.name') </label>
                                                <input type="text" name="name" class="form-control" value="{{auth()->user()->name}}">
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                <button type="submit" class="btn btn-primary btn-block" id="changnamebtn">@lang('site.send')</button>
                                                </div>
                                            </div>
                                        </form>

                                      </div>
                                  </div>


                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane" id="password">
                                    <div class="box">
                                        <div class="box-body">

                                            <form action="" class="form-horizontal" id="chemailform" {{auth()->user()->type != 'student'?'style=display:none':''}}>
                                                @csrf

                                                <input type="hidden" name="id_hid" id="id_hid" value="{{auth()->user()->id}}">
                                                {{-- <input type="hidden" name="action" id="action" value="Edit"> --}}

                                                <div class="form-group">
                                                    <label>@lang('site.enter_your_email') </label>
                                                    <input type="email" name="email" id="email" class="form-control" placeholder="@lang('site.email')">
                                                </div>


                                                <div class="form-group">
                                                  <div>
                                                    <button type="submit" class="btn btn-primary btn-block">@lang('site.send')</button>
                                                  </div>
                                                </div>
                                            </form>
                                            <form action="" method="POST" class="form-horizontal" id="actcodform" style="display: none">
                                                @csrf

                                                <input type="hidden" name="id_hid" id="id_hid" value="{{auth()->user()->id}}">
                                                <input type="hidden" name="action" id="action" value="Edit">

                                                <div class="form-group">
                                                    <label>@lang('site.enter_sent_code') </label>
                                                    <input type="text" name="sentcode" class="form-control" placeholder="@lang('site.active_code')">
                                                </div>


                                                <div class="form-group">
                                                  <div>
                                                    <button type="submit" class="btn btn-primary btn-block">@lang('site.send')</button>
                                                  </div>
                                                </div>
                                            </form>

                                        <form action="" method="POST" class="form-horizontal" id="chpassform" {{auth()->user()->type == 'student'?'style=display:none':''}}>
                                            @csrf

                                            <div class="form-group">
                                                <label>@lang('site.old_password') </label>
                                                <input type="password" name="old_password" class="form-control" >
                                            </div>

                                            <div class="form-group">
                                                <label>@lang('site.new_password') </label>
                                                <input type="password" name="password" class="form-control" >
                                            </div>

                                            <div class="form-group">
                                                <label>@lang('site.password_confirmation') </label>
                                                <input type="password" name="password_confirmation" class="form-control" >
                                            </div>

                                            <div class="form-group">
                                              <div>
                                                <button type="submit" class="btn btn-primary btn-block">@lang('site.send')</button>
                                              </div>
                                            </div>
                                        </form>

                                        </div>
                                    </div>


                            </div>

                            <div class="tab-pane" id="phone">
                                    <div class="box">
                                        <div class="box-body">
                                            <form action="" method="POST" class="form-horizontal" id="chphoneform">
                                                @csrf
                                                <div class="form-group">
                                                    <label>@lang('site.phone') </label>
                                                    <input type="text" name="old_phone" class="form-control" value="{{auth()->user()->phone}}">
                                                </div>

                                                <div class="form-group">
                                                    <label>@lang('site.new_phone') </label>
                                                    <input type="text" name="new_phone" class="form-control">
                                                </div>

                                                <div class="form-group">
                                                <div>
                                                    <button type="submit" class="btn btn-primary btn-block">@lang('site.send')</button>
                                                </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                            </div>
                              <!-- /.tab-pane -->

                              <!-- /.tab-pane -->

                            </div>
                          <!-- /.tab-content -->


                        </div>
                        <!-- /.nav-tabs-custom -->
                      </div>
                      <!-- /.col -->

                  <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="box box-primary card-outline">
                      <div class="box-body box-profile">
                        <div class="text-center">
                          <img class="profile-user-img img-fluid img-circle"
                               src="{{ asset('dashboard/img/user.png') }}"
                               alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center"> {{ auth()->user()->name }}</h3>

                        <p class="text-muted text-center">
                            @if (auth()->user()->type == 'super_admin' || auth()->user()->type == 'admin')
                                @lang('site.admin')
                            @elseif (auth()->user()->type == 'student')
                                @lang('site.student')
                            @elseif (auth()->user()->type == 'doctor')
                                @lang('site.doctor')
                            @elseif (auth()->user()->type == 'parent')
                                @lang('site.parent')
                            @endif
                        </p>

                        @if (auth()->user()->type == 'student' || auth()->user()->type == 'doctor')
                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                <b>@lang('site.subjects')</b>
                                    @if (auth()->user()->type == 'doctor')
                                    <a class="float-right">  {{optional(optional(auth()->user()->toDoctor())->docSubjs())->count()}}</a>
                                    @endif
                                    @if (auth()->user()->type == 'student')
                                    <a class="float-right">  {{optional(optional(auth()->user()->toStudent())->stdSbjs)->count()}}</a>
                                    @endif
                                </li>
                                <li class="list-group-item">
                                <b>@lang('site.lessons')</b>
                                    @if (auth()->user()->type == 'doctor')
                                    <a class="float-right"> {{optional(optional(auth()->user()->toDoctor())->lessons)->count()}}</a>
                                    @endif
                                    @if (auth()->user()->type == 'student')
                                    <a class="float-right">  {{optional(optional(auth()->user()->toStudent())->lessons())->count()}}</a>
                                    @endif
                                </li>
                                <li class="list-group-item">
                                <b>@lang('site.assignments')</b>
                                    @if (auth()->user()->type == 'doctor')
                                    <a class="float-right"> {{optional(optional(auth()->user()->toDoctor())->assignments)->count()}}</a>
                                    @endif
                                    @if (auth()->user()->type == 'student')
                                    <a class="float-right">  {{optional(optional(auth()->user()->toStudent())->assignments())->count()}}</a>
                                    @endif
                                </li>
                            </ul>
                        @endif


                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- About Me Box -->
                    <div class="box box-primary">
                      <div class="box-header">
                        <h3 class="box-title">@lang('site.about-me')</h3>
                      </div>
                      <!-- /.card-header -->
                      <div class="box-body">
                        <strong><i class="fa fa-book mr-1"></i> @lang('site.name')</strong>

                        <p class="text-muted">
                            {{ auth()->user()->name }}
                        </p>

                        <hr>

                        <strong><i class="fa fa-book mr-1"></i> @lang('site.phone')</strong>

                        <p class="text-muted">{{ auth()->user()->phone }}</p>

                        <hr>

                    </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                  </div>
                  <!-- /.col -->

                </div>
                <!-- /.row -->
              </div><!-- /.container-fluid -->
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(function(){

            //function to change profile name
            $('#chnameform').on('submit', function(e){
                e.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var user_id = $('#id_hid').val();

                save_method = 'edit';
                //$('input[name=_method]').val('PATCH');

                //console.log($(this).serialize());

                $.ajax({
                    //method:'POST',
                    //header:{'X-CSRF-TOKEN': token},
                    url : "{{ url('profile/changname'). '/' }}" + user_id,
                    type : "post",
                    data : $(this).serialize(),
                    dataType : "json",
                    success : function(data){
                        if(data.errors){
                            //alert('Data errorsss');
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: data.errors,
                                position:'topCenter',
                            });
                            $('#chnameform')[0].reset();
                        }
                        if(data.success){
                            iziToast.success({
                                timeout: 6000, icon: 'fa fa-check-circle',
                                title: 'Success', message: 'Data updated Successfully',
                                position: 'topCenter',
                            });
                            $('#chnameform')[0].reset();
                            location.reload(true);

                        }
                    },
                    error : function(){
                        //alert('Error Data');
                        iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: 'Error Data',
                                position:'topCenter',
                            });
                            $('#chnameform')[0].reset();
                    }
                });
            });//end of change profile name function

            //function to change the password
            $('#chpassform').on('submit', function(e){
                e.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var user_id = $('#id_hid').val();

                save_method = 'edit';
                //$('input[name=_method]').val('PATCH');

                console.log($(this).serialize());

                $.ajax({
                    //method:'POST',
                    //header:{'X-CSRF-TOKEN': token},
                    url : "{{ url('profile/changpass'). '/' }}" + user_id,
                    type : "post",
                    data : $(this).serialize(),
                    dataType : "json",
                    success : function(data){
                        if(data.errors){
                            //alert(data.errors);
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: data.errors,
                                position:'topCenter',
                            });
                            $('#chpassform')[0].reset();
                        }
                        if(data.success){
                            //alert(data.success);
                            iziToast.success({
                                timeout: 6000, icon: 'fa fa-check-circle',
                                title: 'Success', message: 'Data updated Successfully',
                                position: 'topCenter',
                            });
                            $('#chpassform')[0].reset();
                        }
                    },
                    error : function(){
                        //alert('Error Data');
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: 'برجاء التاكد من حقل التأكيد',
                                position:'topCenter',
                            });
                            $('#chpassform')[0].reset();

                    }
                });
            });//end of cahnge password function

            //function to change the email
            $('#chemailform').on('submit', function(e){
                e.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var user_id = $('#id_hid').val();
                var emailVal = $('#email').val();
                //save_method = 'edit';
                $('input[name=_method]').val('post');

                console.log($(this).serialize());

                $.ajax({
                    //method:'POST',
                    //header:{'X-CSRF-TOKEN': token},
                    url : "{{ url('profile/changemail'). '/' }}" + user_id,
                    type : "post",
                    //data : $(this).serialize(),
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "email": emailVal
                    },
                    dataType : "json",
                    success : function(data){
                        if(data.errors){
                            //alert(data.errors);
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: data.errors,
                                position:'topCenter',
                            });
                            $('#chemailform')[0].reset();
                        }
                        if(data.success){
                            //alert(data.success);
                            iziToast.success({
                                timeout: 6000, icon: 'fa fa-check-circle',
                                title: 'Success', message: 'Data updated Successfully',
                                position: 'topCenter',
                            });
                            $('#chemailform')[0].reset();
                            $('#chemailform').css('display', 'none');
                            $('#actcodform').css('display', 'block');
                            //$('#chpassform').css('display', 'block');

                        }
                    },
                    error : function(errors){
                        //alert('Error Data');
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: errors,
                                position:'topCenter',
                            });
                            $('#chemailform')[0].reset();

                    }
                });
            });//end of cahnge email function

            //function to change the email
            $('#actcodform').on('submit', function(e){
                e.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var user_id = $('#id_hid').val();

                save_method = 'edit';
                //$('input[name=_method]').val('PATCH');

                console.log($(this).serialize());

                $.ajax({
                    //method:'POST',
                    //header:{'X-CSRF-TOKEN': token},
                    url : "{{ url('profile/chactcode'). '/' }}" + user_id,
                    type : "post",
                    data : $(this).serialize(),
                    dataType : "json",
                    success : function(data){
                        if(data.errors){
                            //alert(data.errors);
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: data.errors,
                                position:'topCenter',
                            });
                            $('#actcodform')[0].reset();
                        }
                        if(data.success){
                            //alert(data.success);
                            iziToast.success({
                                timeout: 6000, icon: 'fa fa-check-circle',
                                title: 'Success', message: 'كود التفعيل متطابق يمكنك تغيير كلمة المرور',
                                position: 'topCenter',
                            });
                            $('#actcodform')[0].reset();
                            $('#actcodform').css('display', 'none');
                            //$('#actcodform').css('display', 'block');
                            $('#chpassform').css('display', 'block');

                        }
                    },
                    error : function(errors){
                        //alert('Error Data');
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: 'كود التفعيل غير متطابق',
                                position:'topCenter',
                            });
                            $('#actcodform')[0].reset();

                    }
                });
            });//end of cahnge email function

            //function to change profile phone
            $('#chphoneform').on('submit', function(e){
                e.preventDefault();
                var token = $('meta[name="csrf-token"]').attr('content');
                var user_id = $('#id_hid').val();

                save_method = 'edit';

                $.ajax({
                    //method:'POST',
                    //header:{'X-CSRF-TOKEN': token},
                    url : "{{ url('profile/changphone'). '/' }}" + user_id,
                    type : "post",
                    data : $(this).serialize(),
                    dataType : "json",
                    success : function(data){
                        if(data.errors){
                            //alert('Data errorsss');
                            iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: data.errors,
                                position:'topCenter',
                            });
                            $('#chphoneform')[0].reset();
                        }
                        if(data.success){
                            iziToast.success({
                                timeout: 6000, icon: 'fa fa-check-circle',
                                title: 'Success', message: 'Data updated Successfully',
                                position: 'topCenter',
                            });
                            $('#chphoneform')[0].reset();
                            location.reload(true);
                        }
                    },
                    error : function(){
                        //alert('Error Data');
                        iziToast.error({
                                timeout: 6000,
                                title: 'Error', message: 'Error Data',
                                position:'topCenter',
                            });
                            $('#chphoneform')[0].reset();
                    }
                });
            });//end of change profile phone function

        });
    </script>
@endsection
