<!-- <div x-data="{
    open:false,
    input:'',
    messages:[{from:'bot', text:'👋 Hi! I am LK Health Assistant. Ask me about appointments, services, doctors or emergency.'}],
    faqs:{
        'book appointment': 'You can book an appointment from the \'Book Appointment\' menu in your dashboard, select a doctor and pick a slot.',
        'appointment': 'To manage appointments, go to \'My Appointments\' in your sidebar.',
        'prescription': 'You can view and download all your prescriptions from the Prescriptions tab.',
        'lab': 'Book lab tests from Lab Tests menu. Reports arrive within 24 hours.',
        'pharmacy': 'Order medicines from Pharmacy. Delivery within 4 hours in the city.',
        'emergency': 'For emergencies, press the red Emergency button in the sidebar or call 1800-LK-HEALTH.',
        'doctor': 'Browse all doctors on the Book Appointment page, filter by specialization.',
        'blood': 'Check blood availability and request units from Blood Bank page.',
        'payment': 'Payments are currently in bypass mode. Real gateway integration coming soon.',
        'contact': 'Reach us at contact@lkhealthcare.in or +91 1800-LK-HEALTH.',
        'hello': 'Hello! How can I help you today?',
        'hi': 'Hi there! How can I assist?'
    },
    reply(){
        if(!this.input.trim()) return;
        let q = this.input.toLowerCase();
        this.messages.push({from:'user', text:this.input});
        let answer = 'I am sorry, I could not understand. Please try keywords like: appointment, lab, pharmacy, emergency.';
        for(let key in this.faqs){
            if(q.includes(key)){ answer = this.faqs[key]; break; }
        }
        this.input='';
        setTimeout(()=> this.messages.push({from:'bot', text:answer}), 300);
    }
}" class="fixed bottom-5 right-5 z-50">

    <button x-show="!open" @click="open=true" class="h-14 w-14 rounded-full bg-teal-600 hover:bg-teal-700 text-white shadow-xl flex items-center justify-center">
        <x-icon name="chat" class="h-6 w-6"/>
    </button>

    <div x-show="open" x-transition x-cloak class="w-80 sm:w-96 bg-white rounded-2xl shadow-2xl ring-1 ring-slate-200 overflow-hidden flex flex-col" style="height:460px;">
        <div class="bg-gradient-to-r from-teal-600 to-emerald-500 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center"><x-icon name="chat" class="h-4 w-4"/></div>
                <div>
                    <div class="font-bold text-sm">LK Health Assistant</div>
                    <div class="text-[10px] opacity-80">Online · 24/7</div>
                </div>
            </div>
            <button @click="open=false" class="p-1 rounded hover:bg-white/20"><x-icon name="x" class="h-5 w-5"/></button>
        </div>
        <div class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50" x-ref="log">
            <template x-for="m in messages">
                <div :class="m.from==='bot' ? 'text-left' : 'text-right'">
                    <div :class="m.from==='bot' ? 'bg-white text-slate-700' : 'bg-teal-600 text-white'" class="inline-block px-3 py-2 rounded-2xl text-sm max-w-[75%] shadow-sm" x-text="m.text"></div>
                </div>
            </template>
        </div>
        <form @submit.prevent="reply" class="p-3 border-t border-slate-200 flex items-center gap-2 bg-white">
            <input x-model="input" type="text" placeholder="Type your question..." class="flex-1 rounded-full border-slate-200 text-sm focus:ring-teal-500 focus:border-teal-500">
            <button type="submit" class="h-10 w-10 rounded-full bg-teal-600 text-white flex items-center justify-center hover:bg-teal-700">
                <x-icon name="chevron-down" class="h-4 w-4 -rotate-90"/>
            </button>
        </form>
    </div>
</div> -->

<div x-data="{
    open:false,
    input:'',
    loading:false,

    messages:[
        {
            from:'bot',
            text:'👋 Hi! I am LK Health Assistant. How can I help you today?'
        }
    ],

    async reply(){

        if(!this.input.trim()) return;

        let userMessage = this.input;

        // show user message
        this.messages.push({
            from:'user',
            text:userMessage
        });

        // clear input
        this.input='';

        // loading
        this.loading = true;

        try{

            const response = await fetch(
                'https://openrouter.ai/api/v1/chat/completions',
                {
                    method:'POST',

                    headers:{

                        'Authorization':
                        'Bearer xxxxxxxxxxxx',

                        'Content-Type':'application/json'

                    },

                    body: JSON.stringify({

                        model:'openrouter/free',

                        messages:[

                        {
                        role:'system',

                        content:`

                        You are LK Healthcare Assistant for LK Health Care India.

                        IMPORTANT RULES:

                        - Only answer healthcare, hospital and medical related questions.

                        - If the user asks anything unrelated to healthcare or medical topics, reply ONLY:

                        Sorry, I can only help with hospital and medical related questions.

                        - Detect the user's language automatically.

                        - If the user writes in Hindi, reply ONLY in Hindi.

                        - If the user writes in English, reply ONLY in English.

                        - Do not mix Hindi and English unless the user mixes both.

                        - Use simple natural conversational language.

                        - Keep responses short, clear and helpful.

                        - Sound like a professional hospital assistant.

                        - Do not use robotic or awkward language.

                        - Do not repeat information unnecessarily.

                        - Recommend only the most suitable doctor based on symptoms.

                        - Do not give exact diagnosis with certainty.

                        - Do not strongly prescribe medicines.

                        - Only provide basic safe health guidance.

                        - For serious symptoms, advise immediate doctor consultation.

                        - For emergency symptoms like chest pain, breathing difficulty, unconsciousness, stroke symptoms, seizures, severe bleeding or severe injury, tell the user to immediately visit the nearest hospital or emergency service.

                        AVAILABLE DOCTORS AT LK HEALTH CARE:

                        - Dr. Priyanka Gangwar - Neurologist
                        - Dr. Sanjay Rao - Neurologist
                        - Dr. Neha Gupta - General Physician
                        - Dr. Vikram Singh - Orthopedist
                        - Dr. Priya Iyer - Pediatrician
                        - Dr. Rahul Mehta - Dermatologist
                        - Dr. Anjali Verma - Cardiologist

                        DOCTOR RECOMMENDATION RULES:

                        - neurological symptoms, migraine, severe headache, dizziness, nerve problems → Neurologist

                        - fever, cold, cough, weakness, infection symptoms → General Physician

                        - bone pain, joint pain, fracture, muscle pain → Orthopedist

                        - child health problems → Pediatrician

                        - skin problems, allergy, itching, rashes → Dermatologist

                        - chest pain, heart problems, BP issues → Cardiologist

                        - Always recommend doctors from LK Health Care only.

                        - End important medical responses with:

                        Please consult a qualified doctor at LK Health Care for proper medical guidance and treatment.

                        `
                        },

                        {
                            role:'user',
                            content:userMessage
                        }

                        ]

                    })

                }
            );

            const data = await response.json();

            console.log(data);

            let botReply =
                data?.choices?.[0]?.message?.content
                || 'Sorry, I could not understand.';

            this.messages.push({
                from:'bot',
                text:botReply
            });

        }catch(error){

            console.log(error);

            this.messages.push({
                from:'bot',
                text:'Server error. Please try again.'
            });

        }

        // stop loading
        this.loading = false;

        // auto scroll
        this.$nextTick(() => {

            this.$refs.log.scrollTop =
            this.$refs.log.scrollHeight;

        });

    }

}" class="fixed bottom-5 right-5 z-50">

    <!-- open button -->
    <button
        x-show="!open"
        @click="open=true"
        class="h-14 w-14 rounded-full bg-teal-600 text-white shadow-xl flex items-center justify-center"
    >
        💬
    </button>

    <!-- chatbot -->
    <div
        x-show="open"
        x-transition
        class="w-80 sm:w-96 bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col"
        style="height:460px;"
    >

        <!-- header -->
        <div class="bg-teal-600 text-white px-4 py-3 flex justify-between">

            <div>

                <div class="font-bold">
                    LK Health Assistant
                </div>

                <div class="text-xs opacity-80">
                    Online
                </div>

            </div>

            <button @click="open=false">
                ✕
            </button>

        </div>

        <!-- messages -->
        <div
            class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-100"
            x-ref="log"
        >

            <template x-for="m in messages">

                <div
                    :class="
                    m.from=='bot'
                    ? 'text-left'
                    : 'text-right'
                    "
                >

                    <div

                        :class="
                        m.from=='bot'
                        ? 'bg-white text-black'
                        : 'bg-teal-600 text-white'
                        "

                        class="
                        inline-block
                        px-3
                        py-2
                        rounded-2xl
                        text-sm
                        max-w-[80%]
                        whitespace-pre-line
                        "

                        x-text="m.text"

                    ></div>

                </div>

            </template>

            <!-- loading -->
            <div
                x-show="loading"
                class="text-left"
            >

                <div
                    class="
                    bg-white
                    inline-block
                    px-3
                    py-2
                    rounded-2xl
                    text-sm
                    "
                >

                    Typing...

                </div>

            </div>

        </div>

        <!-- input -->
        <form
            @submit.prevent="reply"
            class="p-3 border-t flex gap-2"
        >

            <input

                x-model="input"

                type="text"

                placeholder="Ask something..."

                class="
                flex-1
                border
                rounded-full
                px-4
                py-2
                text-sm
                "

            >

            <button

                type="submit"

                class="
                bg-teal-600
                text-white
                h-10
                w-10
                rounded-full
                "

            >

                ➤

            </button>

        </form>

    </div>

</div>