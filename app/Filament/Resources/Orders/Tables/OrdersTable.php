<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'confirmed', 'processing', 'packed' => 'warning',
                        'dispatched' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->money('LKR')
                    ->sortable(),
                TextColumn::make('delivery_fee')
                    ->money('LKR')
                    ->sortable(),
                TextColumn::make('total')
                    ->money('LKR')
                    ->sortable(),
                TextColumn::make('courier_name')
                    ->searchable(),
                TextColumn::make('tracking_number')
                    ->searchable(),
                TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(array_combine(Order::STATUSES, array_map(fn (string $status): string => str($status)->headline()->toString(), Order::STATUSES))),
                SelectFilter::make('payment_status')
                    ->options([
                        'cod_pending' => 'COD Pending',
                        'paid' => 'Paid',
                        'partially_paid' => 'Partially Paid',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }
}
