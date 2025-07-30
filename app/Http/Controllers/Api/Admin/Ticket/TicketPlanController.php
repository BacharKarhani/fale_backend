<?php
// app/Http/Controllers/Api/Admin/Ticket/TicketPlanController.php
namespace App\Http\Controllers\Api\Admin\Ticket;

use App\Http\Controllers\Controller;
use App\Models\TicketPlan;
use Illuminate\Http\Request;

class TicketPlanController extends Controller
{
    public function index()
    {
        return response()->json(['data' => TicketPlan::all()]);
    }

    public function show($id)
    {
        $plan = TicketPlan::findOrFail($id);
        return response()->json(['data' => $plan]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'features' => 'required|array'
        ]);

        $plan = TicketPlan::create($request->only('plan_name', 'price', 'features'));

        return response()->json(['message' => 'Ticket plan created', 'data' => $plan], 201);
    }

    public function update(Request $request, $id)
    {
        $plan = TicketPlan::findOrFail($id);

        $request->validate([
            'plan_name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'features' => 'sometimes|array'
        ]);

        $plan->update($request->only('plan_name', 'price', 'features'));

        return response()->json(['message' => 'Ticket plan updated', 'data' => $plan]);
    }

    public function destroy($id)
    {
        $plan = TicketPlan::findOrFail($id);
        $plan->delete();

        return response()->json(['message' => 'Ticket plan deleted']);
    }
}
