@props(['route', 'note' => null])

@php
    $sensoryArousal = [
        'ef_sensory_arousal_under' => 'Under',
        'ef_sensory_arousal_regulated' => 'Regulated',
        'ef_sensory_arousal_over' => 'Over',
    ];

    $sensoryPattern = [
        'ef_sensory_pattern_seeking' => 'Seeking',
        'ef_sensory_pattern_avoidant' => 'Avoidant',
        'ef_sensory_pattern_mixed' => 'Mixed',
    ];

    $regulationSupport = [
        'ef_regulation_movement' => 'Movement',
        'ef_regulation_calming' => 'Calming',
        'ef_regulation_heavy_work' => 'Heavy work',
        'ef_regulation_sensory_break' => 'Sensory break',
        'ef_regulation_independent' => 'Independent',
    ];

    $visualSkills = [
        'ef_visual_discrimination' => 'Visual Discrimination',
        'ef_visual_form_constancy' => 'Form Constancy',
        'ef_visual_memory' => 'Visual Memory',
        'ef_visual_sequential_memory' => 'Visual Sequential Memory',
        'ef_visual_spatial_relations' => 'Spatial Relations',
        'ef_visual_figure_ground' => 'Figure Ground',
        'ef_visual_closure' => 'Visual Closure',
    ];

    $socialSkills = [
        'ef_social_approaches_initiates' => 'Approaches/Initiates',
        'ef_social_concludes_disengages' => 'Concludes/Disengages',
        'ef_social_expresses_emotions' => 'Expresses appropriate emotions',
        'ef_social_replies_maintains_interaction' => 'Replies/Maintains interaction',
        'ef_social_take_turns' => 'Take turns',
        'ef_social_show_politeness' => 'Show politeness',
        'ef_social_ask_questions' => 'Ask Questions',
    ];

    $executiveSkills = [
        'ef_executive_response_inhibition' => 'Response Inhibition',
        'ef_executive_working_memory' => 'Working Memory',
        'ef_executive_task_initiation' => 'Task Initiation',
        'ef_executive_cognitive_flexibility' => 'Cognitive Flexibility',
        'ef_executive_planning_organizing' => 'Planning and Organizing',
        'ef_executive_task_monitoring' => 'Task Monitoring',
        'ef_executive_emotional_regulation' => 'Emotional Regulation',
    ];

    $assistanceOptions = [
        'independent' => 'Independent',
        'hoha' => 'HOHA',
        'model' => 'Model',
        'trial_and_error' => 'Trial and Error',
        'prompts' => 'Prompts',
        'cues' => 'Cues',
        'backward_chaining' => 'Backward Chaining',
    ];

    $levelOptions = [
        'maximal' => 'Maximal',
        'moderate' => 'Moderate',
        'minimal' => 'Minimal',
    ];

    $typeOptions = [
        'physical' => 'Physical',
        'gestural' => 'Gestural',
        'visual' => 'Visual',
        'verbal' => 'Verbal',
    ];

    $assistanceOptionsWithoutBackwardChaining = \Illuminate\Support\Arr::except($assistanceOptions, ['backward_chaining']);
    $assistanceOptionsWithoutHohaAndBackwardChaining = \Illuminate\Support\Arr::except($assistanceOptionsWithoutBackwardChaining, ['hoha']);
@endphp

<form method="POST" action="{{ $route }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <input type="hidden" name="note_section" value="ef">

    <div>
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-gray-600">Goals</p>
    </div>

    <div class="space-y-4">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Sensory processing</p>

                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Arousal</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($sensoryArousal as $field => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="{{ $field }}"
                                    value="1"
                                    @checked(old($field, $note?->{$field}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-gray-500">Pattern</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($sensoryPattern as $field => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="{{ $field }}"
                                    value="1"
                                    @checked(old($field, $note?->{$field}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Regulation support used</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($regulationSupport as $field => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="{{ $field }}"
                                    value="1"
                                    @checked(old($field, $note?->{$field}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <x-input-label :for="'ef_sensory_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ef_sensory_remarks"
                    name="ef_sensory_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_sensory_remarks', $note?->ef_sensory_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Fine Motor Skills</p>
                <p class="text-xs font-semibold uppercase text-gray-500">Specify</p>
                <textarea
                    name="ef_fine_motor_specify"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white/70 px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_fine_motor_specify', $note?->ef_fine_motor_specify) }}</textarea>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Assistance</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($assistanceOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_fine_motor_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_fine_motor_assistance_{$suffix}", $note?->{"ef_fine_motor_assistance_{$suffix}"}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">If prompt/cues: level</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($levelOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_fine_motor_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_fine_motor_assistance_level_{$suffix}", $note?->{"ef_fine_motor_assistance_level_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">type</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($typeOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_fine_motor_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_fine_motor_assistance_type_{$suffix}", $note?->{"ef_fine_motor_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <x-input-label :for="'ef_fine_motor_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ef_fine_motor_remarks"
                    name="ef_fine_motor_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_fine_motor_remarks', $note?->ef_fine_motor_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Cognitive and Processing Skills</p>
                <p class="text-xs font-semibold uppercase text-gray-500">Specify</p>
                <textarea
                    name="ef_cognitive_specify"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white/70 px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_cognitive_specify', $note?->ef_cognitive_specify) }}</textarea>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Assistance</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($assistanceOptionsWithoutHohaAndBackwardChaining as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_cognitive_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_cognitive_assistance_{$suffix}", $note?->{"ef_cognitive_assistance_{$suffix}"}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">If prompt/cues: level</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($levelOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_cognitive_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_cognitive_assistance_level_{$suffix}", $note?->{"ef_cognitive_assistance_level_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">type</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($typeOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_cognitive_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_cognitive_assistance_type_{$suffix}", $note?->{"ef_cognitive_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <x-input-label :for="'ef_cognitive_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ef_cognitive_remarks"
                    name="ef_cognitive_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_cognitive_remarks', $note?->ef_cognitive_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Visual Perceptual Skills and Visual Motor Skills</p>
                <div class="mt-2 grid gap-2 md:grid-cols-2">
                    @foreach ($visualSkills as $field => $label)
                        <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700">
                            <input
                                type="checkbox"
                                name="{{ $field }}"
                                value="1"
                                @checked(old($field, $note?->{$field}))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Assistance</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($assistanceOptionsWithoutBackwardChaining as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_visual_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_visual_assistance_{$suffix}", $note?->{"ef_visual_assistance_{$suffix}"}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">If prompt/cues: level</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($levelOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_visual_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_visual_assistance_level_{$suffix}", $note?->{"ef_visual_assistance_level_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">type</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($typeOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_visual_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_visual_assistance_type_{$suffix}", $note?->{"ef_visual_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <x-input-label :for="'ef_visual_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ef_visual_remarks"
                    name="ef_visual_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_visual_remarks', $note?->ef_visual_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Social Interaction Skills</p>
                <div class="mt-2 grid gap-2 md:grid-cols-2">
                    @foreach ($socialSkills as $field => $label)
                        <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700">
                            <input
                                type="checkbox"
                                name="{{ $field }}"
                                value="1"
                                @checked(old($field, $note?->{$field}))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Assistance</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($assistanceOptionsWithoutHohaAndBackwardChaining as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_social_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_social_assistance_{$suffix}", $note?->{"ef_social_assistance_{$suffix}"}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">If prompt/cues: level</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($levelOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_social_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_social_assistance_level_{$suffix}", $note?->{"ef_social_assistance_level_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">type</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($typeOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_social_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_social_assistance_type_{$suffix}", $note?->{"ef_social_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <x-input-label :for="'ef_social_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ef_social_remarks"
                    name="ef_social_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_social_remarks', $note?->ef_social_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Executive Functioning</p>
                <div class="mt-2 grid gap-2 md:grid-cols-2">
                    @foreach ($executiveSkills as $field => $label)
                        <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-1 text-sm font-medium text-gray-700">
                            <input
                                type="checkbox"
                                name="{{ $field }}"
                                value="1"
                                @checked(old($field, $note?->{$field}))
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Assistance</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($assistanceOptionsWithoutHohaAndBackwardChaining as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_executive_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_executive_assistance_{$suffix}", $note?->{"ef_executive_assistance_{$suffix}"}))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">If prompt/cues: level</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($levelOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_executive_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_executive_assistance_level_{$suffix}", $note?->{"ef_executive_assistance_level_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-3 text-xs font-semibold uppercase text-gray-500">type</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($typeOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ef_executive_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ef_executive_assistance_type_{$suffix}", $note?->{"ef_executive_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <x-input-label :for="'ef_executive_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ef_executive_remarks"
                    name="ef_executive_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ef_executive_remarks', $note?->ef_executive_remarks) }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <x-primary-button>Save Notes</x-primary-button>
    </div>
</form>
