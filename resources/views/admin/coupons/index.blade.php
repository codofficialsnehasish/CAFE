@extends('layouts.app')

@section('title','Coupons')

@section('contents')


    <div class="dashboard-main-body">

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
            <h6 class="fw-semibold mb-0">Coupons</h6>
            <ul class="d-flex align-items-center gap-2">
                <li class="fw-medium">
                    <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="fw-medium">Coupons</li>
            </ul>
        </div>

        <div class="card basic-data-table">
            <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
                <h5 class="card-title mb-0">All Coupons</h5>
                <a href="{{ route('coupon.create') }}" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                    <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                    Add New
                </a>
            </div>
            <div class="card-body table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check style-check d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label">
                                        S.L
                                    </label>
                                </div>
                            </th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Minimum Purchase</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Usage Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td>
                                <div class="form-check style-check d-flex align-items-center">
                                    <input class="form-check-input" type="checkbox">
                                    <label class="form-check-label">
                                        {{ $loop->iteration }}
                                    </label>
                                </div>
                            </td>
                            <td class="text-wrap">{{ $coupon->code }}</td>
                            <td class="text-wrap">{{ $coupon->type }}</td>
                            <td class="text-wrap">{{ $coupon->value }}</td>
                            <td class="text-wrap">{{ $coupon->minimum_purchase }}</td>
                            <td class="text-wrap">{{ $coupon->start_date }}</td>
                            <td class="text-wrap">{{ $coupon->end_date }}</td>
                            <td class="text-wrap">{{ $coupon->usage_type }}</td>
                            <td>{!! check_visibility($coupon->is_active) !!}</td>
                            <td class="text-wrap">{{ format_datetime($coupon->created_at) }}</td>
                            <td>
                                <a href="{{ route('coupon.edit',$coupon->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center">
                                    <iconify-icon icon="lucide:edit"></iconify-icon>
                                </a>
                                <form action="{{ route('coupon.destroy', $coupon->id) }}" onsubmit="return confirm('Are you sure?')" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center" type="submit"><iconify-icon icon="mingcute:delete-2-line"></iconify-icon></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    let table = new DataTable("#dataTable");
</script>
@endsection