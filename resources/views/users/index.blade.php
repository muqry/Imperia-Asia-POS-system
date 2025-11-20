@extends('layouts.app')

@section('content')

<div class="container">
    <div class="col-lg-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 style="float: left">Users</h4>
                        <a href="" style="float: right" class="btn btn-dark" data-toggle="modal" data-target="#addUser">
                            <i class="fa fa-plus"></i>Add New User</a>
                    </div>
                    <div class="card-body">
                        @foreach (['Success', 'Error', 'User Created Successfully', 'User Fail Created'] as $msg)
                            @if (session($msg))
                                <div class="alert alert-{{ $msg == 'Error' || $msg == 'User Fail Created' ? 'danger' : 'success' }} alert-dismissible fade show mt-3" role="alert">
                                    <i class="fa fa-info-circle"></i> {{ session($msg) }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        @endforeach

                        <table class="table table-bordered table-left">
                            <thead>
                                <tr>
                                    <th style="background-color: #8603036e; color: #fff;">#</th>
                                    <th style="background-color: #8603036e; color: #fff;">Name</th>
                                    <th style="background-color: #8603036e; color: #fff;">Email</th>
                                    <!--<th>Phone</th>-->
                                    <th style="background-color: #8603036e; color: #fff;">Role</th>
                                    <th style="background-color: #8603036e; color: #fff;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>@if ($user->is_admin == 1)Admin
                                        @else Cashier
                                        @endif</td>
                                    <td>
                                        <div class="text-center">
                                            <div class="btn-group">
                                                <!--edit-->
                                                <a href="#" class="btn btn-info btn-sm me-2" data-toggle="modal" data-target="#editUser{{ $user->id }}"><i class="fa fa-edit"></i>Edit</a>
                                                <!--Delete-->
                                                <a href="#" data-toggle="modal" data-target="#deleteUser{{ $user->id }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                {{-- Modal of Edit User Detail --}}

                                <div class="modal right fade" id="editUser{{ $user->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="staticBackdropLabel">Edit User</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                {{ $user->id }}
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="">Name</label>
                                                        <input type="text" name="name" id="" value="{{ $user->name }}" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Email</label>
                                                        <input type="email" name="email" id="" value="{{ $user->email }}" class="form-control">
                                                    </div>
                                                    <!--<div class="form-group">
                                                            <label for="">Phone</label>
                                                            <input type="phone" name="phone" id="" value="{{ $user->phone }}" class="form-control">
                                                        </div>-->
                                                    <div class="form-group">
                                                        <label for="">Password</label>
                                                        <input type="password" name="password" readonly id="" {{--readonly--}} value="{{ $user->password }}" class="form-control">
                                                    </div>
                                                    {{--<div class="form-group">
                                                            <label for="">Confirm Password</label>
                                                            <input type="password" name="confirm_password" id="" class="form-control">
                                                        </div>--}}
                                                    <div class="form-group">
                                                        <label for="">Role</label>
                                                        <select name="is_admin" id="" class="form-control">
                                                            <option value="1" @if ($user->is_admin == 1) selected
                                                                @endif>Admin</option>
                                                            <option value="2" @if ($user->is_admin == 2) selected
                                                                @endif>Cashier</option>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-warning btn-block">Update User</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                {{-- Modal of Delete User --}}

                                <div class="modal right fade" id="deleteUser{{ $user->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="staticBackdropLabel">Delete User</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                {{ $user->id }}
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <p>Are you sure you want to delete this <b><i>{{ $user->name }}</i></b>?</p>

                                                    <div class="modal-footer">
                                                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!--Modal of Adding New User -->

    <!-- Modal -->
    <div class="modal right fade" id="addUser" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel">Add User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" name="name" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="email" name="email" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Phone</label>
                            <input type="phone" name="phone" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" name="password" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Confirm Password</label>
                            <input type="password" name="confirm_password" id="" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Role</label>
                            <select name="is_admin" id="" class="form-control">
                                <option value="1">Admin</option>
                                <option value="2">Cashier</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary btn-block">Save User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>






    <style>
        .modal.right .modal-dialog {
            /*position: absolute*/
            top: 0;
            right: 0;
            margin-right: 19vh;
        }

        .modal.fade:not(.in).right .modal-dialog {
            -webkit-transform: translate3d(25%, 0, 0);
            transform: translate3d(25%, 0, 0);
        }
    </style>

    @endsection