@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Ranks" />
    <div
        class="rounded-2xl border border-gray-200 bg-white px-3 py-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-5 sm:py-7 xl:px-10 xl:py-12">
        <div class="mx-auto w-full max-w-[1000px]">
            <x-rank.available-ranks />
        </div>
    </div>
@endsection
