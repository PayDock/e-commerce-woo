<?php

namespace PowerBoard\Helpers;

use PowerBoard\Enums\DSTypes;
use PowerBoard\Enums\FraudTypes;

class ArgsForProcessPayment
{
    public static function prepare(array $args = []): array
    {
        $args = array_change_key_case($args, CASE_LOWER);

        $args['isuserloggedin'] = is_user_logged_in();

        if (!empty($args['card3ds']) && $args['card3ds'] === DSTypes::DISABLE()->name) {
            $args['card3ds'] = '';
        }

        if (!empty($args['cardfraud']) && $args['cardfraud'] === FraudTypes::DISABLE()->name) {
            $args['cardfraud'] = '';
        }
      
        $args['directcharge'] = !empty($args['directcharge']) ? true : false;  
        $args['fraud'] = !empty($args['fraud']) ? true : false;

        $args['carddirectcharge'] = !empty($args['carddirectcharge']) ? true : false;        
        $args['cardsavecard'] = !empty($args['cardsavecard']) ? true : false;        
        $args['cardsavecardchecked'] = !empty($args['cardsavecardchecked']) ? true : false;
        $args['cardsavecardoption'] = isset($args['cardsavecardoption']) ? $args['cardsavecardoption'] : '';
        
        $args['bankaccountsaveaccount'] = !empty($args['bankaccountsaveaccount']) ? true : false;
        $args['bankaccountsavechecked'] = !empty($args['bankaccountsavechecked']) ? true : false;
        $args['bankaccountsaveaccountoption'] = isset($args['bankaccountsaveaccountoption']) ? $args['bankaccountsaveaccountoption'] : '';

        $args['apmsavecard'] = !empty($args['apmsavecard']) ? true : false;        
        $args['apmsavecardchecked'] = !empty($args['apmsavecardchecked']) ? true : false;        

        return $args;
    }
}