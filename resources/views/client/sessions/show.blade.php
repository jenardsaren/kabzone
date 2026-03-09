<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">Completed Session Details</h2>
            <a href="{{ route('client.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-500">Back</a>
        </div>
    </x-slot>

    @php
        $note = $session->note;
        $behaviorLabels = [
            'bo_cooperative' => 'Cooperative',
            'bo_calm_regulated' => 'Calm / Regulated',
            'bo_restless_fidgety' => 'Restless / Fidgety',
            'bo_easily_frustrated' => 'Easily Frustrated',
            'bo_tantrums' => 'Tantrums',
            'bo_meltdowns' => 'Meltdowns',
            'bo_avoidant' => 'Avoidant',
            'bo_aggressive' => 'Aggressive',
        ];
        $sensoryArousalLabels = [
            'Under' => 'ei_sensory_arousal_under',
            'Regulated' => 'ei_sensory_arousal_regulated',
            'Over' => 'ei_sensory_arousal_over',
        ];
        $sensoryPatternLabels = [
            'Seeking' => 'ei_sensory_pattern_seeking',
            'Avoidant' => 'ei_sensory_pattern_avoidant',
            'Mixed' => 'ei_sensory_pattern_mixed',
        ];
        $regulationSupportLabels = [
            'Movement' => 'ei_regulation_movement',
            'Calming' => 'ei_regulation_calming',
            'Heavy work' => 'ei_regulation_heavy_work',
            'Sensory break' => 'ei_regulation_sensory_break',
            'Independent' => 'ei_regulation_independent',
        ];
        $assistanceOptions = [
            'independent' => 'Independent',
            'hoha' => 'Hand-over-hand (HOHA)',
            'model' => 'Model',
            'trial_and_error' => 'Trial and Error',
            'prompts' => 'Prompts',
            'cues' => 'Cues',
        ];
        $promptLevelOptions = [
            'assistance_level_maximal' => 'Maximal',
            'assistance_level_moderate' => 'Moderate',
            'assistance_level_minimal' => 'Minimal',
        ];
        $promptTypeOptions = [
            'assistance_type_physical' => 'Physical',
            'assistance_type_gestural' => 'Gestural',
            'assistance_type_visual' => 'Visual',
            'assistance_type_verbal' => 'Verbal',
        ];
        $cognitiveChecklist = [
            'MSRI' => 'ei_cognitive_msri',
            'Joint Attention' => 'ei_cognitive_joint_attention',
            'Imitation' => 'ei_cognitive_imitation',
            'Concepts' => 'ei_cognitive_concepts',
            'Follow commands' => 'ei_cognitive_follow_commands',
        ];
        $visualChecklist = [
            'Visual Discrimination' => 'ei_visual_discrimination',
            'Form Constancy' => 'ei_visual_form_constancy',
            'Visual Memory' => 'ei_visual_memory',
            'Visual Sequential Memory' => 'ei_visual_sequential_memory',
            'Spatial Relations' => 'ei_visual_spatial_relations',
            'Figure Ground' => 'ei_visual_figure_ground',
            'Visual Closure' => 'ei_visual_closure',
        ];
        $efSensoryArousalLabels = [
            'Under' => 'ef_sensory_arousal_under',
            'Regulated' => 'ef_sensory_arousal_regulated',
            'Over' => 'ef_sensory_arousal_over',
        ];
        $efSensoryPatternLabels = [
            'Seeking' => 'ef_sensory_pattern_seeking',
            'Avoidant' => 'ef_sensory_pattern_avoidant',
            'Mixed' => 'ef_sensory_pattern_mixed',
        ];
        $efRegulationSupportLabels = [
            'Movement' => 'ef_regulation_movement',
            'Calming' => 'ef_regulation_calming',
            'Heavy work' => 'ef_regulation_heavy_work',
            'Sensory break' => 'ef_regulation_sensory_break',
            'Independent' => 'ef_regulation_independent',
        ];
        $efSocialLabels = [
            'Approaches / Initiates' => 'ef_social_approaches_initiates',
            'Concludes / Disengages' => 'ef_social_concludes_disengages',
            'Expresses appropriate emotions' => 'ef_social_expresses_emotions',
            'Replies / Maintains interaction' => 'ef_social_replies_maintains_interaction',
            'Take turns' => 'ef_social_take_turns',
            'Show politeness' => 'ef_social_show_politeness',
        ];
        $efExecutiveLabels = [
            'Response Inhibition' => 'ef_executive_response_inhibition',
            'Working Memory' => 'ef_executive_working_memory',
            'Task Initiation' => 'ef_executive_task_initiation',
            'Cognitive Flexibility' => 'ef_executive_cognitive_flexibility',
            'Planning & Organizing' => 'ef_executive_planning_organizing',
            'Task Monitoring' => 'ef_executive_task_monitoring',
            'Emotional Regulation' => 'ef_executive_emotional_regulation',
        ];
        $collectFlags = fn (string $prefix, array $map) => $note
            ? collect($map)
                ->filter(fn ($label, $suffix) => $note->{$prefix . $suffix})
                ->values()
                ->all()
            : [];
        $behaviorObservations = $note ? collect($behaviorLabels)->filter(fn ($label, $field) => $note->{$field})->values()->all() : [];
        $grossMotorAssistance = $collectFlags('ei_gross_motor_assistance_', $assistanceOptions);
        $grossMotorLevels = $collectFlags('ei_gross_motor_', $promptLevelOptions);
        $grossMotorTypes = $collectFlags('ei_gross_motor_', $promptTypeOptions);
        $fineMotorAssistance = $collectFlags('ei_fine_motor_assistance_', $assistanceOptions);
        $fineMotorLevels = $collectFlags('ei_fine_motor_', $promptLevelOptions);
        $fineMotorTypes = $collectFlags('ei_fine_motor_', $promptTypeOptions);
        $workBehaviorAssistance = $collectFlags('ei_work_behavior_assistance_', $assistanceOptions);
        $workBehaviorLevels = $collectFlags('ei_work_behavior_', $promptLevelOptions);
        $workBehaviorTypes = $collectFlags('ei_work_behavior_', $promptTypeOptions);
        $cognitiveAssistance = $collectFlags('ei_cognitive_assistance_', $assistanceOptions);
        $cognitiveLevels = $collectFlags('ei_cognitive_', $promptLevelOptions);
        $cognitiveTypes = $collectFlags('ei_cognitive_', $promptTypeOptions);
        $visualAssistance = $collectFlags('ei_visual_assistance_', $assistanceOptions);
        $visualLevels = $collectFlags('ei_visual_', $promptLevelOptions);
        $visualTypes = $collectFlags('ei_visual_', $promptTypeOptions);
        $efFineMotorAssistance = $collectFlags('ef_fine_motor_assistance_', $assistanceOptions);
        $efFineMotorLevels = $collectFlags('ef_fine_motor_', $promptLevelOptions);
        $efFineMotorTypes = $collectFlags('ef_fine_motor_', $promptTypeOptions);
        $efCognitiveAssistance = $collectFlags('ef_cognitive_assistance_', $assistanceOptions);
        $efCognitiveLevels = $collectFlags('ef_cognitive_', $promptLevelOptions);
        $efCognitiveTypes = $collectFlags('ef_cognitive_', $promptTypeOptions);
        $efVisualAssistance = $collectFlags('ef_visual_assistance_', $assistanceOptions);
        $efVisualLevels = $collectFlags('ef_visual_', $promptLevelOptions);
        $efVisualTypes = $collectFlags('ef_visual_', $promptTypeOptions);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                    @if ($session->payment_status === 'Paid')
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
                            <div class="flex items-center gap-3 text-sm text-emerald-700">
                                <span class="h-6 w-6 rounded-full bg-white text-emerald-600 flex items-center justify-center text-lg">✓</span>
                                <div>
                                    <p class="font-semibold">Payment received</p>
                                    <p class="text-xs text-emerald-600">Thank you! The payment for this session is confirmed.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-4 sm:grid-cols-2 text-sm">
                            <div>
                                <p class="text-gray-500">Date</p>
                                <p class="font-medium text-gray-900">{{ $session->date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Time</p>
                                <p class="font-medium text-gray-900">{{ $session->formatted_time }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Type</p>
                                <p class="font-medium text-gray-900">{{ str($session->type->value)->headline() }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">OT</p>
                                <p class="font-medium text-gray-900">{{ $session->therapist?->full_name }}</p>
                            </div>
                            <div class='hidden'>
                                <p class="text-gray-500">KSA</p>
                                <p class="font-medium text-gray-900">{{ $session->assistant?->full_name ?? 'Unassigned' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Status</p>
                                <p><x-status-badge :status="$session->status" /></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Description & Summary</h3>
                        <p class="mt-3 text-sm text-gray-700">{{ $session->description ?: 'No description was provided.' }}</p>
                        <div class="mt-6 rounded-lg bg-gray-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Summary</p>
                            <p class="mt-2 text-sm text-gray-700">{{ $session->summary ?: 'No summary was provided yet.' }}</p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Behavior Observations</h3>
                        <p class="mt-1 text-sm text-gray-500">Captured by the OT session note to highlight how your child responded during the visit.</p>
                        @if (!empty($behaviorObservations))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($behaviorObservations as $observation)
                                    <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700">
                                        {{ $observation }}
                                    </span>
                                @endforeach
                            </div>
                            @if ($note?->bo_other_details)
                                <p class="mt-3 text-sm text-gray-600">
                                    <span class="font-semibold text-gray-700">Other details:</span>
                                    {{ $note->bo_other_details }}
                                </p>
                            @endif
                        @else
                            <p class="mt-3 text-sm text-gray-500">No observations were recorded for this session.</p>
                        @endif
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">Activities &amp; Management</h3>
                        <p class="mt-1 text-sm text-gray-500">An overview of technique, pacing, or supports the OT used so you can follow up consistently at home.</p>
                        <p class="mt-3 text-sm text-gray-700">
                            {{ $note?->am_activities_and_management ?: 'No activities or management notes were recorded.' }}
                        </p>
                    </div>
                @if ($session->type !== \App\Enums\SessionType::Initial)
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Session Notes</h3>
                            <div class="mt-4 space-y-6">
                                @if ($hasEiNotes || $hasEfNotes)
                                    @if ($hasEiNotes)
                                        <div class="space-y-4">
                                            <div class="rounded-md bg-indigo-50 p-4 text-sm text-indigo-700">
                                                EI Session Notes were submitted for this session. Only the EI section below is displayed.
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Sensory processing</h4>
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        @foreach ($sensoryArousalLabels as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-semibold text-indigo-700">Arousal {{ $label }}</span>
                                                            @endif
                                                        @endforeach

                                                        @foreach ($sensoryPatternLabels as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-teal-100 px-3 py-0.5 text-xs font-semibold text-teal-700">Pattern {{ $label }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        @foreach ($regulationSupportLabels as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-xs font-semibold text-yellow-700">{{ $label }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <p class="mt-3 text-sm text-gray-600">{{ $note->ei_sensory_remarks ?: 'No remarks were added.' }}</p>
                                                </div>

                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Gross Motor Skills</h4>
                                                    <p class="mt-2 text-sm text-gray-700">{{ $note->ei_gross_motor_specify ?: 'Not specified.' }}</p>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $grossMotorAssistance ? implode(', ', $grossMotorAssistance) : 'Not recorded.' }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">Prompt/cues level: {{ $grossMotorLevels ? implode(', ', $grossMotorLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $grossMotorTypes ? implode(', ', $grossMotorTypes) : 'Not recorded.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_gross_motor_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Fine Motor Skills</h4>
                                                    <p class="mt-2 text-sm text-gray-700">{{ $note->ei_fine_motor_specify ?: 'Not specified.' }}</p>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $fineMotorAssistance ? implode(', ', $fineMotorAssistance) : 'Not recorded.' }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">Prompt/cues level: {{ $fineMotorLevels ? implode(', ', $fineMotorLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $fineMotorTypes ? implode(', ', $fineMotorTypes) : 'Not recorded.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_fine_motor_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Work Behaviors</h4>
                                                    <div class="mt-2 text-sm text-gray-700 space-y-1">
                                                        <p>Frustration tolerance: {{ $note->ei_work_behavior_frustration_tolerance !== null ? $note->ei_work_behavior_frustration_tolerance . '%' : 'Not recorded.' }}</p>
                                                        <p>Impulse control: {{ $note->ei_work_behavior_impulse_control !== null ? $note->ei_work_behavior_impulse_control . '%' : 'Not recorded.' }}</p>
                                                        <p>Compliance: {{ $note->ei_work_behavior_compliance !== null ? $note->ei_work_behavior_compliance . '%' : 'Not recorded.' }}</p>
                                                    </div>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $workBehaviorAssistance ? implode(', ', $workBehaviorAssistance) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Prompt/cues level: {{ $workBehaviorLevels ? implode(', ', $workBehaviorLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $workBehaviorTypes ? implode(', ', $workBehaviorTypes) : 'Not recorded.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_work_behavior_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Cognitive & Processing</h4>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach ($cognitiveChecklist as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-semibold text-indigo-700">{{ $label }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $cognitiveAssistance ? implode(', ', $cognitiveAssistance) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Prompt/cues level: {{ $cognitiveLevels ? implode(', ', $cognitiveLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $cognitiveTypes ? implode(', ', $cognitiveTypes) : 'Not recorded.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_cognitive_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Visual Perceptual / Motor</h4>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach ($visualChecklist as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-semibold text-emerald-700">{{ $label }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $visualAssistance ? implode(', ', $visualAssistance) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Prompt/cues level: {{ $visualLevels ? implode(', ', $visualLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $visualTypes ? implode(', ', $visualTypes) : 'Not recorded.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_visual_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Language & Communication</h4>
                                                    <p class="mt-2 text-sm text-gray-700">{{ $note->ei_language_specify ?: 'Not specified.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_language_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Play Skills</h4>
                                                    <p class="mt-2 text-sm text-gray-700">{{ $note->ei_play_specify ?: 'Not specified.' }}</p>
                                                    <p class="mt-3 text-xs text-gray-600 italic">{{ $note->ei_play_remarks ?: 'No remarks.' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($hasEfNotes)
                                        <div class="space-y-4">
                                            <div class="rounded-md bg-indigo-50 p-4 text-sm text-indigo-700">
                                                EF Session Notes were submitted for this session. Only the EF section below is displayed.
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <h4 class="text-base font-semibold text-gray-800">Sensory processing</h4>
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach ($efSensoryArousalLabels as $label => $field)
                                                        @if ($note?->{$field})
                                                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-semibold text-indigo-700">Arousal {{ $label }}</span>
                                                        @endif
                                                    @endforeach

                                                    @foreach ($efSensoryPatternLabels as $label => $field)
                                                        @if ($note?->{$field})
                                                            <span class="inline-flex items-center rounded-full bg-teal-100 px-3 py-0.5 text-xs font-semibold text-teal-700">Pattern {{ $label }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach ($efRegulationSupportLabels as $label => $field)
                                                        @if ($note?->{$field})
                                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-0.5 text-xs font-semibold text-yellow-700">{{ $label }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Fine Motor Skills</h4>
                                                    <p class="mt-2 text-sm text-gray-700">{{ $note->ef_fine_motor_specify ?: 'Not specified.' }}</p>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $efFineMotorAssistance ? implode(', ', $efFineMotorAssistance) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Prompt/cues level: {{ $efFineMotorLevels ? implode(', ', $efFineMotorLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $efFineMotorTypes ? implode(', ', $efFineMotorTypes) : 'Not recorded.' }}</p>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Cognitive & Processing</h4>
                                                    <p class="mt-2 text-sm text-gray-700">{{ $note->ef_cognitive_specify ?: 'Not specified.' }}</p>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $efCognitiveAssistance ? implode(', ', $efCognitiveAssistance) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Prompt/cues level: {{ $efCognitiveLevels ? implode(', ', $efCognitiveLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $efCognitiveTypes ? implode(', ', $efCognitiveTypes) : 'Not recorded.' }}</p>
                                                </div>
                                            </div>

                                            <div class="grid gap-4 md:grid-cols-2">
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Visual Perceptual / Motor</h4>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach ($visualChecklist as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-0.5 text-xs font-semibold text-emerald-700">{{ $label }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                    <p class="mt-2 text-xs text-gray-500">Assistance: {{ $efVisualAssistance ? implode(', ', $efVisualAssistance) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Prompt/cues level: {{ $efVisualLevels ? implode(', ', $efVisualLevels) : 'Not recorded.' }}</p>
                                                    <p class="text-xs text-gray-500">Types: {{ $efVisualTypes ? implode(', ', $efVisualTypes) : 'Not recorded.' }}</p>
                                                </div>
                                                <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                    <h4 class="text-base font-semibold text-gray-800">Social Interaction</h4>
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        @foreach ($efSocialLabels as $label => $field)
                                                            @if ($note?->{$field})
                                                                <span class="inline-flex items-center rounded-full bg-pink-100 px-3 py-0.5 text-xs font-semibold text-pink-700">{{ $label }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-white p-4">
                                                <h4 class="text-base font-semibold text-gray-800">Executive Functioning</h4>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach ($efExecutiveLabels as $label => $field)
                                                        @if ($note?->{$field})
                                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-0.5 text-xs font-semibold text-blue-700">{{ $label }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <p class="text-sm text-gray-500">No detailed notes were added for this session.</p>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900">Plan</h3>
                            <p class="mt-2 text-sm text-gray-700">{{ $note?->plan ?: 'No plan was added yet.' }}</p>
                        </div>
                    @else
                        <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
                            <p class="text-sm font-semibold text-yellow-800">Initial sessions do not include detailed notes.</p>
                            <p class="mt-1 text-sm text-yellow-700">Only Regular sessions display the OT’s note breakdown.</p>
                        </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
