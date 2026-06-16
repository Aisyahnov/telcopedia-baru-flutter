<?php

namespace App\Services;

use App\Models\Voucher;

class VoucherService
{
    public function getAll($perPage = 10)
    {
        return Voucher::latest()->paginate($perPage);
    }

    public function createVoucher($adminId, array $data)
    {
        $data['created_by'] = $adminId;
        return Voucher::create($data);
    }

    public function updateVoucher($voucherId, array $data)
    {
        $voucher = Voucher::findOrFail($voucherId);
        $voucher->update($data);
        return $voucher;
    }

    public function deleteVoucher($voucherId)
    {
        $voucher = Voucher::findOrFail($voucherId);
        return $voucher->delete();
    }

    public function validateVoucher($code)
    {
        $voucher = Voucher::where('code', $code)->first();
        if (!$voucher) return ['valid' => false, 'message' => 'Voucher tidak ditemukan'];
        if ($voucher->valid_until !== null && $voucher->valid_until < now()) {
            return ['valid' => false, 'message' => 'Voucher sudah kadaluarsa'];
        }
        return ['valid' => true, 'voucher' => $voucher];
    }
}
