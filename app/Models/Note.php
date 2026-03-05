<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    public const BEHAVIOR_FIELDS = [
        'bo_cooperative',
        'bo_calm_regulated',
        'bo_restless_fidgety',
        'bo_easily_frustrated',
        'bo_tantrums',
        'bo_meltdowns',
        'bo_avoidant',
        'bo_aggressive',
        'bo_other',
    ];

    public const EF_BOOLEAN_FIELDS = [
        'ef_sensory_arousal_under',
        'ef_sensory_arousal_regulated',
        'ef_sensory_arousal_over',
        'ef_sensory_pattern_seeking',
        'ef_sensory_pattern_avoidant',
        'ef_sensory_pattern_mixed',
        'ef_regulation_movement',
        'ef_regulation_calming',
        'ef_regulation_heavy_work',
        'ef_regulation_sensory_break',
        'ef_regulation_independent',
        'ef_fine_motor_assistance_independent',
        'ef_fine_motor_assistance_hoha',
        'ef_fine_motor_assistance_model',
        'ef_fine_motor_assistance_trial_and_error',
        'ef_fine_motor_assistance_prompts',
        'ef_fine_motor_assistance_cues',
        'ef_fine_motor_assistance_level_maximal',
        'ef_fine_motor_assistance_level_moderate',
        'ef_fine_motor_assistance_level_minimal',
        'ef_fine_motor_assistance_type_physical',
        'ef_fine_motor_assistance_type_gestural',
        'ef_fine_motor_assistance_type_visual',
        'ef_fine_motor_assistance_type_verbal',
        'ef_cognitive_assistance_independent',
        'ef_cognitive_assistance_hoha',
        'ef_cognitive_assistance_model',
        'ef_cognitive_assistance_trial_and_error',
        'ef_cognitive_assistance_prompts',
        'ef_cognitive_assistance_cues',
        'ef_cognitive_assistance_level_maximal',
        'ef_cognitive_assistance_level_moderate',
        'ef_cognitive_assistance_level_minimal',
        'ef_cognitive_assistance_type_physical',
        'ef_cognitive_assistance_type_gestural',
        'ef_cognitive_assistance_type_visual',
        'ef_cognitive_assistance_type_verbal',
        'ef_visual_discrimination',
        'ef_visual_form_constancy',
        'ef_visual_memory',
        'ef_visual_sequential_memory',
        'ef_visual_spatial_relations',
        'ef_visual_figure_ground',
        'ef_visual_closure',
        'ef_visual_assistance_independent',
        'ef_visual_assistance_hoha',
        'ef_visual_assistance_model',
        'ef_visual_assistance_trial_and_error',
        'ef_visual_assistance_prompts',
        'ef_visual_assistance_cues',
        'ef_visual_assistance_level_maximal',
        'ef_visual_assistance_level_moderate',
        'ef_visual_assistance_level_minimal',
        'ef_visual_assistance_type_physical',
        'ef_visual_assistance_type_gestural',
        'ef_visual_assistance_type_visual',
        'ef_visual_assistance_type_verbal',
        'ef_social_approaches_initiates',
        'ef_social_concludes_disengages',
        'ef_social_expresses_emotions',
        'ef_social_replies_maintains_interaction',
        'ef_social_take_turns',
        'ef_social_show_politeness',
        'ef_social_assistance_independent',
        'ef_social_assistance_hoha',
        'ef_social_assistance_model',
        'ef_social_assistance_trial_and_error',
        'ef_social_assistance_prompts',
        'ef_social_assistance_cues',
        'ef_social_assistance_level_maximal',
        'ef_social_assistance_level_moderate',
        'ef_social_assistance_level_minimal',
        'ef_social_assistance_type_physical',
        'ef_social_assistance_type_gestural',
        'ef_social_assistance_type_visual',
        'ef_social_assistance_type_verbal',
        'ef_executive_response_inhibition',
        'ef_executive_working_memory',
        'ef_executive_task_initiation',
        'ef_executive_cognitive_flexibility',
        'ef_executive_planning_organizing',
        'ef_executive_task_monitoring',
        'ef_executive_emotional_regulation',
        'ef_executive_assistance_independent',
        'ef_executive_assistance_hoha',
        'ef_executive_assistance_model',
        'ef_executive_assistance_trial_and_error',
        'ef_executive_assistance_prompts',
        'ef_executive_assistance_cues',
        'ef_executive_assistance_level_maximal',
        'ef_executive_assistance_level_moderate',
        'ef_executive_assistance_level_minimal',
        'ef_executive_assistance_type_physical',
        'ef_executive_assistance_type_gestural',
        'ef_executive_assistance_type_visual',
        'ef_executive_assistance_type_verbal',
    ];

    public const EF_TEXT_FIELDS = [
        'ef_fine_motor_specify',
        'ef_cognitive_specify',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'session_id',
        'content',
        'bo_other_details',
        'am_activities_and_management',
        ...self::EF_TEXT_FIELDS,
        ...self::BEHAVIOR_FIELDS,
        ...self::EF_BOOLEAN_FIELDS,
    ];

    protected function casts(): array
    {
        return array_fill_keys(
            array_merge(self::BEHAVIOR_FIELDS, self::EF_BOOLEAN_FIELDS),
            'bool'
        );
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
