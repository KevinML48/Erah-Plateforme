<?php
declare(strict_types=1);

namespace App\Enums;

enum PointTransactionType: string
{
    case BetWin = 'bet_win';
    case PredictionWin = 'prediction_win';
    case PredictionStake = 'prediction_stake';
    case TicketStake = 'ticket_stake';
    case TicketPayout = 'ticket_payout';
    case TicketRefund = 'ticket_refund';
    case RewardRedeem = 'reward_redeem';
    case RewardRefund = 'reward_refund';
    case MissionComplete = 'mission_complete';
    case RewardPurchase = 'reward_purchase';
    case AdminAdjustment = 'admin_adjustment';
}
