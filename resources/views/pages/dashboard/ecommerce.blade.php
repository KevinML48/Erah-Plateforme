@extends('layouts.app')

@section('content')
  <div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12">
      <x-common.quick-actions />
    </div>
    <div class="col-span-12">
      <x-common.player-focus />
    </div>

    <div class="col-span-12 space-y-6 xl:col-span-7">
      <x-ecommerce.ecommerce-metrics />
      <x-ecommerce.monthly-sale />
    </div>
    <div class="col-span-12 xl:col-span-5 xl:self-stretch">
        <x-ecommerce.monthly-target />
    </div>

    <div class="col-span-12">
      <x-ecommerce.statistics-chart />
    </div>

    <div class="col-span-12">
      <x-rank.available-ranks />
    </div>

  </div>
@endsection
