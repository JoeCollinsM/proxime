<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'date',
        'branch_id',
        'category_id',
        'amount',
        'note',
        'created_by'
    ];

    public function category()
    {
        return $this->hasOne('App\Models\ExpenseCategory', 'id', 'category_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public static function getExpenseAnalysis(array $data)
    {
        $authuser = Auth::user();

        $expenses = Expense::where('created_by', $authuser->getCreatedBy());

        if ($data['branch_id'] != '-1') {
            $expenses = $expenses->where('branch_id', $data['branch_id']);
        }

        if ($data['expense_category_id'] != '-1') {
            $expenses = $expenses->where('category_id', $data['expense_category_id']);
        }

        if ($data['start_date'] != '' && $data['end_date'] != '') {
            $expenses = $expenses->whereDate('date', '>=', $data['start_date'])->whereDate('date', '<=', $data['end_date']);
        } else if ($data['start_date'] != '' || $data['end_date'] != '') {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $expenses = $expenses->whereDate('date', '=', $date);
        }

        $expenseArray = [];

        $total_expense_amount = 0;

        if ($expenses->count() > 0) {

            foreach ($expenses->get() as $counter => $expense) {

                $total_expense_amount += $expense->amount;

                $expenseArray[$counter]['date'] = Auth::user()->dateFormat($expense->date);
                $expenseArray[$counter]['expense_category'] = $expense->category->name;
                $expenseArray[$counter]['note'] = '<p class="expense_note">' . $expense->note . '</p>';
                $expenseArray[$counter]['created_by'] = ucfirst($expense->user->name);
                $expenseArray[$counter]['amount'] = Auth::user()->priceFormat($expense->amount);
            }
        }


        $data['draw']            = 1;
        $data['recordsTotal']    = count($expenseArray);
        $data['recordsFiltered'] = count($expenseArray);
        $data['totalExpenseAmount'] = Auth::user()->priceFormat($total_expense_amount);
        $data['data']            = $expenseArray;

        return $data;
    }
    public static function branchname($branch)
    {
        $categoryArr  = explode(',', $branch);
        $unitRate = 0;
        foreach($categoryArr as $branch)
        {
            $branch          = Branch::find($branch);
            $unitRate        = (!empty($branch->name) ? $branch->name :'');
            
        }

        return $unitRate;
    }
    public static function expensecategory($expensecategory)
    {
        $categoryArr  = explode(',', $expensecategory);
        $unitRate = 0;
        foreach($categoryArr as $expensecategory)
        {
            $expensecategory    = ExpenseCategory::find($expensecategory);
            $unitRate        = (!empty($expensecategory->name) ? $expensecategory->name :'');
            
        }

        return $unitRate;
    }
}
