@php
    $user = auth()->user();
    $showTutorial = !$user->onboarding_completed;
    
    // Définir les étapes selon le rôle
    $steps = [];
    
    if ($user->hasRole('demandeur')) {
        $steps = [
            [
                'title' => 'Bienvenue sur SENDAPTNAPT !',
                'description' => 'Cette application vous permet de gérer vos Demandes d\'Autorisation Pour Travaux (DAPT) et de suivre leur traitement.',
                'icon' => 'home',
                'target' => null,
            ],
            [
                'title' => 'Créer une demande',
                'description' => 'Cliquez sur "Nouvelle demande" pour créer une DAPT. Remplissez les informations sur les travaux prévus.',
                'icon' => 'plus',
                'target' => '[data-tutorial="new-demande"]',
            ],
            [
                'title' => 'Suivre vos demandes',
                'description' => 'Consultez l\'état de vos demandes dans la liste. Vous serez notifié à chaque changement de statut.',
                'icon' => 'list',
                'target' => '[data-tutorial="demandes-list"]',
            ],
            [
                'title' => 'Déclarer une absence',
                'description' => 'En cas d\'absence, vous pouvez désigner un intérimaire qui effectuera vos tâches à votre place.',
                'icon' => 'calendar',
                'target' => '[data-tutorial="absences"]',
            ],
            [
                'title' => 'Vous êtes prêt !',
                'description' => 'Consultez la documentation pour plus de détails. N\'hésitez pas à envoyer des observations si vous avez des questions.',
                'icon' => 'check',
                'target' => null,
            ],
        ];
    } elseif ($user->hasRole('desa')) {
        $steps = [
            [
                'title' => 'Bienvenue DESA !',
                'description' => 'Votre rôle est de traiter les demandes DAPT et de créer les Notes d\'Arrêt Pour Travaux (NAPT).',
                'icon' => 'home',
                'target' => null,
            ],
            [
                'title' => 'Traiter les demandes',
                'description' => 'Consultez les demandes reçues et traitez-les en créant des NAPT ou en les retournant.',
                'icon' => 'document',
                'target' => '[data-tutorial="demandes"]',
            ],
            [
                'title' => 'Créer une NAPT',
                'description' => 'Pour chaque DAPT acceptée, créez une NAPT en précisant les dates, installations et intervenants.',
                'icon' => 'clipboard',
                'target' => '[data-tutorial="notes"]',
            ],
            [
                'title' => 'Diffusion hebdomadaire',
                'description' => 'Envoyez la diffusion des NAPT de la semaine aux groupes concernés.',
                'icon' => 'mail',
                'target' => '[data-tutorial="diffusion"]',
            ],
            [
                'title' => 'Vous êtes prêt !',
                'description' => 'Consultez la documentation pour maîtriser toutes les fonctionnalités.',
                'icon' => 'check',
                'target' => null,
            ],
        ];
    } else {
        $steps = [
            [
                'title' => 'Bienvenue !',
                'description' => 'SENDAPTNAPT vous permet de gérer le processus DAPT/NAPT de bout en bout.',
                'icon' => 'home',
                'target' => null,
            ],
            [
                'title' => 'Votre tableau de bord',
                'description' => 'Retrouvez ici vos tâches en attente et vos statistiques.',
                'icon' => 'chart',
                'target' => '[data-tutorial="dashboard"]',
            ],
            [
                'title' => 'Notifications',
                'description' => 'Restez informé des actions requises grâce aux notifications en temps réel.',
                'icon' => 'bell',
                'target' => '[data-tutorial="notifications"]',
            ],
            [
                'title' => 'Documentation',
                'description' => 'Consultez la documentation pour en savoir plus sur le workflow.',
                'icon' => 'book',
                'target' => null,
            ],
        ];
    }
@endphp

@if($showTutorial)
<div x-data="{ 
    open: true, 
    currentStep: 0,
    steps: {{ json_encode($steps) }},
    nextStep() {
        if (this.currentStep < this.steps.length - 1) {
            this.currentStep++;
        }
    },
    prevStep() {
        if (this.currentStep > 0) {
            this.currentStep--;
        }
    },
    completeTutorial() {
        fetch('{{ route('onboarding.complete') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            this.open = false;
        });
    }
}" 
x-show="open"
x-cloak
class="fixed inset-0 z-50">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/70"></div>
    
    <!-- Modal -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full transform transition-all relative z-10"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Progress -->
            <div class="h-1 bg-gray-200">
                <div class="h-1 bg-gradient-to-r from-senelec-purple to-senelec-orange transition-all duration-500"
                     :style="'width: ' + ((currentStep + 1) / steps.length * 100) + '%'"></div>
            </div>
            
            <!-- Content -->
            <div class="p-8">
                <!-- Icon -->
                <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-gradient-to-br from-senelec-purple to-senelec-orange flex items-center justify-center">
                    <template x-if="steps[currentStep].icon === 'home'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'plus'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'list'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'calendar'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'check'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'document'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'clipboard'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'mail'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'chart'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'bell'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </template>
                    <template x-if="steps[currentStep].icon === 'book'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </template>
                </div>
                
                <!-- Text -->
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-3 font-['Rajdhani']" x-text="steps[currentStep].title"></h2>
                <p class="text-gray-600 text-center leading-relaxed" x-text="steps[currentStep].description"></p>
                
                <!-- Steps indicator -->
                <div class="flex justify-center gap-2 mt-6">
                    <template x-for="(step, index) in steps" :key="index">
                        <button @click="currentStep = index"
                                :class="index === currentStep ? 'bg-senelec-purple w-8' : 'bg-gray-300 w-2'"
                                class="h-2 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-8 py-4 bg-gray-50 flex items-center justify-between">
                <button @click="completeTutorial()" 
                        class="text-sm text-gray-500 hover:text-gray-700">
                    Passer le tutoriel
                </button>
                
                <div class="flex gap-3">
                    <button @click="prevStep()" 
                            x-show="currentStep > 0"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                        Précédent
                    </button>
                    <button @click="currentStep < steps.length - 1 ? nextStep() : completeTutorial()"
                            class="px-6 py-2 text-white rounded-lg transition" style="background-color: #E85D04;">
                        <span x-text="currentStep < steps.length - 1 ? 'Suivant' : 'Commencer'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
