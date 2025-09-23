<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
	use HasFactory;

	protected $fillable = [
		'application_id',
		'attorney_id',
		'applicant_id',
		'case_manager_id',
		'status',
		'requested_by',
		'topic',
		'notes',
		'scheduled_for',
		'duration_minutes',
		'provider',
		'join_url',
		'start_url',
		'provider_meeting_id',
	];

	protected $casts = [
		'scheduled_for' => 'datetime',
	];

	// Status constants
	public const STATUS_REQUESTED = 'requested';
	public const STATUS_SCHEDULED = 'scheduled';
	public const STATUS_APPROVED = 'approved';
	public const STATUS_DECLINED = 'declined';
	public const STATUS_CANCELED = 'canceled';
	public const STATUS_COMPLETED = 'completed';

	public function application()
	{
		return $this->belongsTo(Application::class);
	}

	public function attorney()
	{
		return $this->belongsTo(User::class, 'attorney_id');
	}

	public function applicant()
	{
		return $this->belongsTo(User::class, 'applicant_id');
	}

	public function caseManager()
	{
		return $this->belongsTo(User::class, 'case_manager_id');
	}
}

