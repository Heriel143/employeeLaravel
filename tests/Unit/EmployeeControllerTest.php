<?php

namespace Tests\Unit\Http\Controllers\Api;

use Tests\TestCase;
use Mockery;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Request;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method of EmployeeController.
     * This test checks if the index method correctly retrieves and returns a list of employees.
     */
    public function test_index()
    {
        // Arrange: Create 10 employee records in the database
        Employee::factory()->count(10)->create();
        $controller = new EmployeeController();

        // Act: Call the index method
        $response = $controller->index();

        // Assert: Check if the response contains exactly 10 employee resources
        $this->assertCount(10, $response->resource);
    }

    /**
     * Test the store method of EmployeeController.
     * This test checks if the store method correctly creates a new employee and returns the proper response.
     */
    public function test_store()
    {
        // Arrange: Mock the StoreEmployeeRequest and specify what the validated method should return
        $request = Mockery::mock(StoreEmployeeRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'department' => 'Marketing',
            'salary' => 50000.00,
        ]);

        $controller = new EmployeeController();

        // Act: Call the store method
        $response = $controller->store($request);

        // Assert: Check if the response is an instance of EmployeeResource and if the employee exists in the database
        $this->assertInstanceOf(EmployeeResource::class, $response);
        $this->assertDatabaseHas('employees', ['email' => 'john.doe@example.com']);
    }

    /**
     * Test the show method of EmployeeController.
     * This test checks if the show method correctly retrieves and returns a specific employee.
     */
    public function test_show()
    {
        // Arrange: Create a new employee
        $employee = Employee::factory()->create();
        $controller = new EmployeeController();

        // Act: Call the show method with the created employee
        $response = $controller->show($employee);

        // Assert: Check if the response is an EmployeeResource and if the returned employee matches the created one
        $this->assertInstanceOf(EmployeeResource::class, $response);
        $this->assertEquals($employee->id, $response->resource->id);
    }

    /**
     * Test the update method of EmployeeController.
     * This test checks if the update method correctly updates an existing employee.
     */
    public function test_update()
    {
        // Arrange: Create an employee and mock the UpdateEmployeeRequest
        $employee = Employee::factory()->create();
        $request = Mockery::mock(UpdateEmployeeRequest::class);
        $request->shouldReceive('validated')->once()->andReturn([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'department' => 'Sales',
            'salary' => 60000.00,
        ]);

        $controller = new EmployeeController();

        // Act: Call the update method
        $response = $controller->update($request, $employee);

        // Assert: Check if the response is an EmployeeResource and if the updated employee exists in the database
        $this->assertInstanceOf(EmployeeResource::class, $response);
        $this->assertDatabaseHas('employees', ['email' => 'jane.smith@example.com']);
    }

    /**
     * Test the destroy method of EmployeeController.
     * This test checks if the destroy method correctly deletes an employee.
     */
    public function test_destroy()
    {
        // Arrange: Create an employee
        $employee = Employee::factory()->create();
        $controller = new EmployeeController();

        // Act: Call the destroy method
        $response = $controller->destroy($employee);

        // Assert: Check if the employee is deleted from the database and if the response status is 204 No Content
        $this->assertNull(Employee::find($employee->id));
        $this->assertEquals(204, $response->getStatusCode());
    }
}
