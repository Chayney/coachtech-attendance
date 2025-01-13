@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/userlist.css') }}">
@endsection

@section('content')
    <div class="contact-form__content">
        <h1 class="page-title">スタッフ一覧</h1>
        <div class="attend-table">
            <table class="attend-table__inner">
                <tr class="attend-table__row">
                    <th class="attend-table__header">名前</th>
                    <th class="attend-table__header">メールアドレス</th>
                    <th class="attend-table__header">月次勤怠</th>
                </tr>
                @foreach ($users as $user)
                    <tr class="attend-table__row">                
                        <td class="attend-table__item">
                            {{ $user['name'] }}
                        </td>
                        <td class="attend-table__item">
                            {{ $user['email'] }}
                        </td>
                        <td class="attend-table__item">
                            <form action="/admin/attendance/staff/{id}" method="get">
                                <input type="hidden" name="id" value="{{ $user['id'] }}">
                                <button type="submit" class="detail-btn" name="name" value="{{ $user['name'] }}">
                                    <label>詳細</label>
                                </button>
                            </form>
                        </td>              
                    </tr>
                @endforeach              
            </table>
        </div>
    </div>
@endsection