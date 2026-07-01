@extends('Superadmin.layouts.master')

@section('title' , 'Audit Logs - Control Deck')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/superadmin/analytics.css') }}">
@endsection

@section('content')
    <div style="max-width: 1600px; margin: 0 auto; display: flex; flex-direction: column; gap: 2rem;">

        <div class="content-header">
            <div>
                <h2 class="header-title">Audit Logs</h2>
                <p class="header-subtitle">
                    <span class="subtitle-dot"></span>
                    Audit Logs to Monitor Admin.
                </p>
            </div>
        </div>
    </div>


@endsection