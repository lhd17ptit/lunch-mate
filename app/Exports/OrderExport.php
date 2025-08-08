<?php

namespace App\Exports;

use App\Models\Order;
use App\Repositories\OrderServingRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderExport  implements WithTitle, ShouldAutoSize, WithHeadings, FromCollection, WithMapping
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Thống kê doanh thu';
    }

    /**
     * Set header columns
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'STT',
            'Tên khách hàng',
            'Món',
            'Tiền'
        ];
    }

    public function collection()
    {
        $data = $this->data;

        $orderServingRepository = app(OrderServingRepository::class);

        $items = $orderServingRepository->query();

        if (!empty($data['status'])) {
            $items->whereHas('order', function ($query) use ($data) {
                $query->where('status', $data['status']);
            });
        }

        if (!empty($data['search_date'])) {
            $items->whereDate('created_at', $data['search_date']);
        }

        if (!empty($data['search_floor'])) {
            $items->whereHas('user', function ($query) use ($data) {
                $query->where('floor_id', $data['search_floor']);
            });
        }
        
        $items = $items->orderBy('id', 'asc')->get();

        $items->each(function ($item, $key) {
            $item->index = $key + 1;
        });

        return $items;
    }

    public function map($item): array
    {
        $foodCategoryName = '';
        $foodItemName = [];
        foreach ($item->orderServingFoodItems as $orderServingFoodItem) {
            if (!empty($orderServingFoodItem->foodItem->foodCategory) && empty($foodCategoryName) && $orderServingFoodItem->foodItem->foodCategory->key != 'com') {
                $foodCategoryName = $orderServingFoodItem->foodItem->foodCategory->name;
            }
            $foodItemName[] = $orderServingFoodItem->foodItem->name ?? null;
        }
        
        if (!empty($foodItemName)) {
            $foodItemName = implode(' + ', array_filter($foodItemName));
        }

        $note = '';
        if (!empty($item->note)) {
            $note = ' (' . $item->note . ')';
        }

        $foods = !empty($foodItemName) ? $foodCategoryName . ' ' . $foodItemName . ($note) : $foodCategoryName . ($note);

        return [
            $item->index,
            $item->user->name ?? 'Chưa xác định',
            $foods,
            $item->amount.',000'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
            },
        ];
    }
}
