@props(['route', 'note' => null])

@php
    $sensoryArousal = [
        'ei_sensory_arousal_under' => 'Under',
        'ei_sensory_arousal_regulated' => 'Regulated',
        'ei_sensory_arousal_over' => 'Over',
    ];

    $sensoryPattern = [
        'ei_sensory_pattern_seeking' => 'Seeking',
        'ei_sensory_pattern_avoidant' => 'Avoidant',
        'ei_sensory_pattern_mixed' => 'Mixed',
    ];

    $regulationSupport = [
        'ei_regulation_movement' => 'Movement',
        'ei_regulation_calming' => 'Calming',
        'ei_regulation_heavy_work' => 'Heavy work',
        'ei_regulation_sensory_break' => 'Sensory break',
        'ei_regulation_independent' => 'Independent',
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

    $cognitiveSkills = [
        'ei_cognitive_msri' => 'MSRI',
        'ei_cognitive_joint_attention' => 'Joint Attention',
        'ei_cognitive_imitation' => 'Imitation',
        'ei_cognitive_concepts' => 'Concepts',
        'ei_cognitive_follow_commands' => 'Follow commands',
    ];

    $visualSkills = [
        'ei_visual_discrimination' => 'Visual Discrimination',
        'ei_visual_form_constancy' => 'Form Constancy',
        'ei_visual_memory' => 'Visual Memory',
        'ei_visual_sequential_memory' => 'Visual Sequential Memory',
        'ei_visual_spatial_relations' => 'Spatial Relations',
        'ei_visual_figure_ground' => 'Figure Ground',
        'ei_visual_closure' => 'Visual Closure',
    ];
@endphp

<form method="POST" action="{{ $route }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <input type="hidden" name="note_section" value="ei">

    <div>
        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-gray-600">Goals</p>
    </div>

    <div class="space-y-3">
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

            <div>
                <x-input-label :for="'ei_sensory_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ei_sensory_remarks"
                    name="ei_sensory_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_sensory_remarks', $note?->ei_sensory_remarks) }}</textarea>
            </div>
        </div>

        @foreach (['gross', 'fine'] as $type)
            <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="space-y-3 border-r border-gray-100 pr-4">
                    <p class="text-sm font-semibold text-gray-700">{{ ucfirst($type) }} Motor Skills</p>
                    <p class="text-xs font-semibold uppercase text-gray-500">Specify</p>
                    <textarea
                        name="ei_{{ $type }}_motor_specify"
                        rows="3"
                        class="mt-2 block w-full rounded-md border border-gray-300 bg-white/70 px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old("ei_{$type}_motor_specify", $note?->{"ei_{$type}_motor_specify"}) }}</textarea>
                </div>

                <div class="space-y-3 pl-4">
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                        <p class="font-semibold text-gray-700">Assistance</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($assistanceOptions as $suffix => $label)
                                <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                    <input
                                        type="checkbox"
                                        name="ei_{{ $type }}_motor_assistance_{{ $suffix }}"
                                        value="1"
                                        @checked(old("ei_{$type}_motor_assistance_{$suffix}", $note?->{"ei_{$type}_motor_assistance_{$suffix}"}))
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
                                        name="ei_{{ $type }}_motor_assistance_level_{{ $suffix }}"
                                        value="1"
                                        @checked(old("ei_{$type}_motor_assistance_level_{$suffix}", $note?->{"ei_{$type}_motor_assistance_level_{$suffix}"}))
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
                                        name="ei_{{ $type }}_motor_assistance_type_{{ $suffix }}"
                                        value="1"
                                        @checked(old("ei_{$type}_motor_assistance_type_{$suffix}", $note?->{"ei_{$type}_motor_assistance_type_{$suffix}"}))
                                        class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <x-input-label :for="'ei_'.$type.'_motor_remarks'" :value="__('Remarks')" />
                    <textarea
                        name="ei_{{ $type }}_motor_remarks"
                        rows="3"
                        class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                    >{{ old("ei_{$type}_motor_remarks", $note?->{"ei_{$type}_motor_remarks"}) }}</textarea>
                </div>
            </div>
        @endforeach

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Work Behaviors</p>
                <div class="space-y-2 text-sm">
                    @foreach ([
                        'ei_work_behavior_frustration_tolerance' => 'Frustration Tolerance',
                        'ei_work_behavior_impulse_control' => 'Impulse Control',
                        'ei_work_behavior_compliance' => 'Compliance',
                    ] as $field => $label)
                        <label class="flex items-center gap-3">
                            <span>{{ $label }}</span>
                            <input
                                type="number"
                                name="{{ $field }}"
                                min="0"
                                max="100"
                                value="{{ old($field, $note?->{$field}) }}"
                                class="w-20 rounded-md border border-gray-300 px-2 py-1 text-xs focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <span>%</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 pl-4">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm">
                    <p class="font-semibold text-gray-700">Assistance</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @php
                            $workBehaviorAssistanceOptions = \Illuminate\Support\Arr::only($assistanceOptions, ['independent', 'prompts', 'cues']);
                        @endphp
                        @foreach ($workBehaviorAssistanceOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ei_work_behavior_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_work_behavior_assistance_{$suffix}", $note?->{"ei_work_behavior_assistance_{$suffix}"}))
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
                                    name="ei_work_behavior_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_work_behavior_assistance_level_{$suffix}", $note?->{"ei_work_behavior_assistance_level_{$suffix}"}))
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
                                    name="ei_work_behavior_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_work_behavior_assistance_type_{$suffix}", $note?->{"ei_work_behavior_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <x-input-label :for="'ei_work_behavior_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ei_work_behavior_remarks"
                    name="ei_work_behavior_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_work_behavior_remarks', $note?->ei_work_behavior_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Cognitive and Processing Skills</p>
                <div class="mt-2 grid gap-2 md:grid-cols-2">
                    @foreach ($cognitiveSkills as $field => $label)
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
                        @foreach ($assistanceOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ei_cognitive_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_cognitive_assistance_{$suffix}", $note?->{"ei_cognitive_assistance_{$suffix}"}))
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
                                    name="ei_cognitive_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_cognitive_assistance_level_{$suffix}", $note?->{"ei_cognitive_assistance_level_{$suffix}"}))
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
                                    name="ei_cognitive_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_cognitive_assistance_type_{$suffix}", $note?->{"ei_cognitive_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <x-input-label :for="'ei_cognitive_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ei_cognitive_remarks"
                    name="ei_cognitive_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_cognitive_remarks', $note?->ei_cognitive_remarks) }}</textarea>
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
                        @foreach ($assistanceOptions as $suffix => $label)
                            <label class="inline-flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-1 text-sm font-medium text-gray-700">
                                <input
                                    type="checkbox"
                                    name="ei_visual_assistance_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_visual_assistance_{$suffix}", $note?->{"ei_visual_assistance_{$suffix}"}))
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
                                    name="ei_visual_assistance_level_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_visual_assistance_level_{$suffix}", $note?->{"ei_visual_assistance_level_{$suffix}"}))
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
                                    name="ei_visual_assistance_type_{{ $suffix }}"
                                    value="1"
                                    @checked(old("ei_visual_assistance_type_{$suffix}", $note?->{"ei_visual_assistance_type_{$suffix}"}))
                                    class="h-3 w-3 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div>
                <x-input-label :for="'ei_visual_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ei_visual_remarks"
                    name="ei_visual_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_visual_remarks', $note?->ei_visual_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Language and Communication Skill</p>
                <x-input-label :for="'ei_language_specify'" :value="__('Specify')" />
                <textarea
                    id="ei_language_specify"
                    name="ei_language_specify"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_language_specify', $note?->ei_language_specify) }}</textarea>
            </div>

            <div></div>

            <div>
                <x-input-label :for="'ei_language_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ei_language_remarks"
                    name="ei_language_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_language_remarks', $note?->ei_language_remarks) }}</textarea>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-[minmax(0,3fr),minmax(0,2fr),minmax(0,1.5fr)] rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div class="space-y-3 border-r border-gray-100 pr-4">
                <p class="text-sm font-semibold text-gray-700">Play Skills</p>
                <x-input-label :for="'ei_play_specify'" :value="__('Specify')" />
                <textarea
                    id="ei_play_specify"
                    name="ei_play_specify"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_play_specify', $note?->ei_play_specify) }}</textarea>
            </div>

            <div></div>

            <div>
                <x-input-label :for="'ei_play_remarks'" :value="__('Remarks')" />
                <textarea
                    id="ei_play_remarks"
                    name="ei_play_remarks"
                    rows="3"
                    class="mt-2 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500"
                >{{ old('ei_play_remarks', $note?->ei_play_remarks) }}</textarea>
            </div>
        </div>

    </div>

    <div class="flex justify-end">
        <x-primary-button>Save Notes</x-primary-button>
    </div>
</form>
