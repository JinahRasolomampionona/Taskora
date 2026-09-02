<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Task;
use PHPUnit\Framework\TestCase;

/**
 * Règles de transition : elles sont la colonne vertébrale du produit,
 * elles doivent être vérifiables sans base de données.
 */
class TaskStatusTest extends TestCase
{
    public function test_the_six_business_statuses_exist(): void
    {
        $this->assertSame(
            ['pending', 'todo', 'in_progress', 'completed', 'cancelled', 'archived'],
            array_map(fn (TaskStatus $s) => $s->value, TaskStatus::cases())
        );
    }

    public function test_an_agent_follows_the_nominal_path(): void
    {
        $this->assertTrue(TaskStatus::Pending->canTransitionTo(TaskStatus::Todo, UserRole::Agent));
        $this->assertTrue(TaskStatus::Todo->canTransitionTo(TaskStatus::InProgress, UserRole::Agent));
        $this->assertTrue(TaskStatus::InProgress->canTransitionTo(TaskStatus::Completed, UserRole::Agent));
    }

    public function test_an_agent_cannot_shortcut_or_archive(): void
    {
        $this->assertFalse(TaskStatus::Pending->canTransitionTo(TaskStatus::Completed, UserRole::Agent));
        $this->assertFalse(TaskStatus::Todo->canTransitionTo(TaskStatus::Completed, UserRole::Agent));
        $this->assertFalse(TaskStatus::Completed->canTransitionTo(TaskStatus::Archived, UserRole::Agent));
        $this->assertFalse(TaskStatus::InProgress->canTransitionTo(TaskStatus::Cancelled, UserRole::Agent));
    }

    public function test_a_client_can_only_cancel_a_pending_request(): void
    {
        $this->assertTrue(TaskStatus::Pending->canTransitionTo(TaskStatus::Cancelled, UserRole::Client));
        $this->assertFalse(TaskStatus::Todo->canTransitionTo(TaskStatus::Cancelled, UserRole::Client));
        $this->assertFalse(TaskStatus::InProgress->canTransitionTo(TaskStatus::Completed, UserRole::Client));
        $this->assertSame([], TaskStatus::Completed->allowedTransitions(UserRole::Client));
    }

    public function test_an_admin_has_the_widest_rights(): void
    {
        $this->assertTrue(TaskStatus::Completed->canTransitionTo(TaskStatus::Archived, UserRole::Admin));
        $this->assertTrue(TaskStatus::Cancelled->canTransitionTo(TaskStatus::Pending, UserRole::Admin));
        $this->assertTrue(TaskStatus::Archived->canTransitionTo(TaskStatus::Pending, UserRole::Admin));
    }

    public function test_labels_are_in_french(): void
    {
        $this->assertSame('En attente', TaskStatus::Pending->label());
        $this->assertSame('À faire', TaskStatus::Todo->label());
        $this->assertSame('En cours', TaskStatus::InProgress->label());
        $this->assertSame('Terminer', TaskStatus::Completed->label());
        $this->assertSame('Annuler', TaskStatus::Cancelled->label());
        $this->assertSame('Archive', TaskStatus::Archived->label());
    }

    public function test_durations_are_rendered_for_humans(): void
    {
        $this->assertSame('45 min', Task::minutesForHumans(45));
        $this->assertSame('2 h', Task::minutesForHumans(120));
        $this->assertSame('1 j', Task::minutesForHumans(480));
        $this->assertSame('1 j 2 h', Task::minutesForHumans(600));
        $this->assertNull(Task::minutesForHumans(0));
        $this->assertNull(Task::minutesForHumans(null));
    }
}
