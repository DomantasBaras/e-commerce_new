<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use App\Http\Controllers\CartController;

class TransferSessionCartToUser
{
    protected $cartController;

    public function __construct(CartController $cartController)
    {
        $this->cartController = $cartController;
    }

    public function handle(Authenticated $event)
    {
        $this->cartController->transferSessionCartToUser($event->user->id);
    }
}
