<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method retrieves a paginated list of employees
     * and returns them as a collection of EmployeeResource.
     */
    public function index()
    {
        // Retrieves employees and paginates the results, showing 10 per page
        return EmployeeResource::collection(Employee::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * This method handles creating a new employee using validated request data.
     *
     * @param StoreEmployeeRequest $request
     * @return EmployeeResource
     */
    public function store(StoreEmployeeRequest $request)
    {
        // Creates a new employee with validated data from the request
        $employee = Employee::create($request->validated());

        // Returns the newly created employee as a resource
        return new EmployeeResource($employee);
    }

    /**
     * Display the specified resource.
     *
     * This method retrieves and displays a specific employee.
     *
     * @param Employee $employee
     * @return EmployeeResource
     */
    public function show(Employee $employee)
    {
        // Returns the specified employee as a resource
        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * This method updates an existing employee with validated request data.
     *
     * @param UpdateEmployeeRequest $request
     * @param Employee $employee
     * @return EmployeeResource
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        // Updates the employee with validated data from the request
        $employee->update($request->validated());

        // Returns the updated employee as a resource
        return new EmployeeResource($employee);
    }

    /**
     * Remove the specified resource from storage.
     *
     * This method deletes a specified employee from the database.
     *
     * @param Employee $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        // Deletes the specified employee
        $employee->delete();

        // Returns a 204 No Content response indicating successful deletion
        return response()->noContent();
    }
}
