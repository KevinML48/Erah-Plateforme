@extends('layouts.app')

@section('title', $title ?? 'ERAH')

@section('content')
    {{ $slot }}
@endsection
