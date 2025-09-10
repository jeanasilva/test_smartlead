<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskAssigned extends Notification
{
    use Queueable;

    protected $task;
    protected $assignedBy;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task, $assignedBy = null)
    {
        $this->task = $task;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Tarefa Atribuída - ' . $this->task->title)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Uma nova tarefa foi atribuída a você.')
            ->line('**Título:** ' . $this->task->title)
            ->line('**Descrição:** ' . ($this->task->description ?: 'Sem descrição'))
            ->line('**Status:** ' . $this->getStatusLabel($this->task->status))
            ->line('**Prioridade:** ' . $this->getPriorityLabel($this->task->priority));

        if ($this->assignedBy) {
            $message->line('**Atribuída por:** ' . $this->assignedBy->name);
        }

        if ($this->task->due_date) {
            $message->line('**Prazo:** ' . $this->task->due_date->format('d/m/Y'));
        }

        $message->line('**Empresa:** ' . $this->task->company->name)
            ->action('Ver Tarefa', url('/tasks/' . $this->task->id))
            ->line('Boa sorte com a execução da tarefa!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'assigned_by' => $this->assignedBy ? $this->assignedBy->name : null,
            'company_name' => $this->task->company->name,
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'concluida' => 'Concluída'
        ];
        return $labels[$status] ?? $status;
    }

    private function getPriorityLabel($priority)
    {
        $labels = [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta'
        ];
        return $labels[$priority] ?? $priority;
    }
}
