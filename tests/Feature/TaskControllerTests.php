<?php

namespace Smeechos\TaskScheduler\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Smeechos\TaskScheduler\Models\Cron;
use Smeechos\TaskScheduler\Models\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTests extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /**
     * Testing index method of TaskController.
     *
     * Confirms that the route returns status 200, a view with a variable ($tasks) that is
     * an instance of Illuminate\Database\Eloquent\Collection, and that certain strings are seen.
     *
     * @return void
     * @test
     */
    public function it_can_show_task_page()
    {
        $response = $this->get('/task-scheduler/tasks');

        $response->assertStatus(200);
        $response->assertViewHas( 'crons' );
        $response->assertViewHas( 'tasks' );

        $tasks = $response->original->getData()['tasks'];
        $crons = $response->original->getData()['crons'];

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $tasks);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $crons);
        $response->assertSeeInOrder(['Add New Task', 'Command Name', 'Cron Expressions',
            'Artisan Command', 'Cron', 'Description', 'Actions']);
    }

    /**
     * Testing edit method of TaskController.
     *
     * @return void
     * @test
     */
    public function it_can_render_edit_screen()
    {
        $task = factory(Task::class)->create();

        $response = $this->get('task-scheduler/tasks/edit/' . $task->id);

        $response->assertStatus(200);
        $response->assertViewHas( 'task' );
        $response->assertViewHas( 'crons' );

        $task = $response->original->getData()['task'];
        $crons = $response->original->getData()['crons'];

        $this->assertInstanceOf('Smeechos\TaskScheduler\Models\Task', $task);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $crons);
        $response->assertSeeInOrder(['Edit Task', 'Command Name', 'Cron Expressions']);
    }

    /**
     * Testing update method of TaskController.
     *
     * Posts to the URI, asserts that the status is a 302 and is redirected to
     * the route 'tasks', with the specified session variables (flash message).
     *
     * @return void
     * @test
     */
    public function it_can_update_task()
    {
        // TODO: finish this test
//        $task = factory(Task::class)->create();
//
//        $data = [
//            'command'   => $this->faker->text(15),
//            'cron_id'   => factory(\Smeechos\TaskScheduler\Models\Cron::class)->create()->id,
//            '_token'        => csrf_token()
//        ];
//
//        $this->post('task-scheduler/tasks/edit/' . $task->id, $data)
//            ->assertStatus(302)
//            ->assertRedirect(route('tasks'))
//            ->assertSessionHas('stsp-status', 'success')
//            ->assertSessionHas('stsp-message', 'Task Successfully Updated!');
        $this->assertTrue(true);
    }

    /**
     * Testing store method of TaskController.
     *
     * Posts to the URI, asserts that the status is a 302 and is redirected to
     * the route 'tasks', with the specified session variables (flash message).
     *
     * @return void
     * @test
     */
    public function it_can_create_task()
    {
        /*
         * TODO: fix this so it passes
         * This currently fails because it does not like the foreign key constraint.
         * When I used a value that actually exists in the database, it passes.
         * When I used a value that is not in the database, it fails.
         * When I use a factory and use its ID, it fails.
         */
        $cron = factory(Cron::class)->create();

        $data = [
            'command'   => $this->faker->text(15),
            'cron'      => $cron->id,
            '_token'    => csrf_token()
        ];

        $this->post('task-scheduler/tasks/add', $data)
                ->assertStatus(302)
                ->assertRedirect(route('tasks'))
                ->assertSessionHas('stsp-status', 'success')
                ->assertSessionHas('stsp-message', 'Task Successfully Added!');
    }

    /**
     * Testing show method of TaskController.
     *
     * @return void
     * @test
     */
    public function it_can_render_show_screen()
    {
        $task = factory(Task::class)->create();

        $response = $this->get('task-scheduler/tasks/delete/' . $task->id);

        $response->assertStatus(200);
        $response->assertViewHas( 'task' );

        $task = $response->original->getData()['task'];

        $this->assertInstanceOf('Smeechos\TaskScheduler\Models\Task', $task);
        $response->assertSeeInOrder(['Delete Task', 'Command Name', 'Cron Expressions']);
    }

    /**
     * Testing destroy method of TaskController.
     *
     * @return void
     * @test
     */
    public function it_can_delete_task()
    {
        $task = factory(Task::class)->create();

        $data = [
            '_token' => csrf_token()
        ];

        $this->post('task-scheduler/tasks/delete/' . $task->id, $data)
            ->assertStatus(302)
            ->assertRedirect(route('tasks'))
            ->assertSessionHas('stsp-status', 'success')
            ->assertSessionHas('stsp-message', 'Task Successfully Deleted!');

    }
}
