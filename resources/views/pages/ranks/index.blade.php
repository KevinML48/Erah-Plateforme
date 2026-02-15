@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Ranks" />
    <div
        class="premium-card px-3 py-4 sm:px-5 sm:py-7 xl:px-10 xl:py-12">
        <div class="mx-auto w-full max-w-[1000px]">
            <x-rank.available-ranks />
        </div>
    </div>
@endsection
