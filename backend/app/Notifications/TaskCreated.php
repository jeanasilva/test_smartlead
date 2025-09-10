<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Task;

class TaskCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $task;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
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
        return (new MailMessage)
                    ->subject('Nova Tarefa Atribuida - ' . $this->task->title)
                    ->greeting('Ola ' . $notifiable->name . '!')
                    ->line('Uma nova tarefa foi atribuida a voce.')
                    ->line('Titulo: ' . $this->task->title)
                    ->line('Descricao: ' . ($this->task->description ?: 'Sem descricao'))
                    ->line('Prioridade: ' . ucfirst($this->task->priority))
                    ->line('Status: ' . ucfirst(str_replace('_', ' ', $this->task->status)))
                    ->when($this->task->due_date, function ($message) {
                        return $message->line('Prazo: ' . $this->task->due_date->format('d/m/Y'));
                    })
                    ->line('Empresa: ' . $this->task->company->name)
                    ->action('Ver Tarefa', url('/tasks/' . $this->task->id))
                    ->line('Obrigado por usar nosso sistema!');
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
            'company_name' => $this->task->company->name,
        ];
    }
}
