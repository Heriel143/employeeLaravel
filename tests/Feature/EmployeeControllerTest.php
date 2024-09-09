<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the index method to fetch a paginated list of employees.
     */
    public function test_can_list_employees()
    {
        // Arrange: Create employees
        Employee::factory()->count(15)->create();

        // Act: Send a GET request to fetch employees
        $response = $this->getJson('/api/employees');

        // Assert: Verify the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'first_name', 'last_name', 'email', 'department', 'salary']
                ],
                'meta' => ['current_page', 'last_page'],
                'links' => ['first', 'last']
            ]);
    }

    /**
     * Test the store method to create a new employee.
     */
    public function test_can_create_employee()
    {
        // Arrange: Prepare the request data
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'department' => 'Marketing',
            'salary' => 50000.00,
        ];

        // Act: Send a POST request to create an employee
        $response = $this->postJson('/api/employees', $data);

        // Assert: Check if the employee is created and data is correct
        $response->assertStatus(201)
            ->assertJsonFragment(['first_name' => 'John', 'email' => 'john.doe@example.com']);

        $this->assertDatabaseHas('employees', $data);
    }

    /**
     * Test the show method to display a single employee.
     */
    public function test_can_show_employee()
    {
        // Arrange: Create an employee
        $employee = Employee::factory()->create();

        // Act: Send a GET request to show the employee
        $response = $this->getJson("/api/employees/{$employee->id}");

        // Assert: Check if the correct employee data is returned
        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $employee->id, 'first_name' => $employee->first_name]);
    }

    /**
     * Test the update method to update an existing employee.
     */
    public function test_can_update_employee()
    {
        // Arrange: Create an employee
        $employee = Employee::factory()->create();

        // Prepare updated data
        $updatedData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'department' => 'HR',
            'salary' => 60000.00,
        ];

        // Act: Send a PUT request to update the employee
        $response = $this->putJson("/api/employees/{$employee->id}", $updatedData);

        // Assert: Verify the response and that the employee data is updated
        $response->assertStatus(200)
            ->assertJsonFragment(['first_name' => 'Jane', 'email' => 'jane.smith@example.com']);

        $this->assertDatabaseHas('employees', $updatedData);
    }

    /**
     * Test the destroy method to delete an employee.
     */
    public function test_can_delete_employee()
    {
        // Arrange: Create an employee
        $employee = Employee::factory()->create();

        // Act: Send a DELETE request to delete the employee
        $response = $this->deleteJson("/api/employees/{$employee->id}");

        // Assert: Verify the response status and that the employee is deleted
        $response->assertStatus(204);
        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }
}
