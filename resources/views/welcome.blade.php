<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
                @font-face {
            font-family: 'Pelak';
            src: url('/css/fonts/PelakFA.woff2') format('woff2')
        }

        .font-sans {
            font-family: 'Pelak' !IMPORTANT;
        }

        body {
            font-family: 'Pelak' !IMPORTANT;
        }
        /* Custom CSS for A4 size */
        .a4-canvas {
          width: 210mm; /* A4 width */
          height: 297mm; /* A4 height */
          background-color: white;
          border: 1px solid #ccc;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .merged {
            background-color: #eef2ff; /* Light blue for merged/break slots */
        }
    </style>
    @vite(['resources/css/app.css'])
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>

<body class="antialiased">
    <div id="app">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-6">
                <div class="mt-8 dark:bg-gray-800 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 bg-[#f2eecb] mx-auto a4-canvas">
                        <!-- Left Column -->
                        <div class="p-6 h-full">
                            <div class="mb-4 flex">
                                <div class="logo border-2 border-black p-10"></div>
                                <h1 class="font-bold text-3xl pr-2">
                                    جعبه <br>
                                    زمان 
                                </h1>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-xl mb-2">اولویت‌ها</h3>
                                <div class="grid grid-cols-1 border border-b-0">
                                    <textarea class="w-full resize-none px-2 py-3.5 focus:outline-none border-b" v-for="(priority, index) in priorities" :key="index" v-model="priorities[index]"></textarea>

                                </div>
                            </div>

                            <div class="mb-4 h-full">
                                <h3 class="text-xl mb-2">بارش فکری</h3>
                                <textarea v-model="brainDump" cols="45" rows="20"
                                    class="bg-dotted-spacing-5 resize-none h-full bg-dotted-gray-300 w-full px-2 py-3.5 focus:outline-none border"></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="p-6">
                            <div class="flex gap-2 mb-4">
                                <label for="">تاریخ: </label>
                                <input v-model="date" type="text" class="border-b w-full focus:outline-none" readonly>
                            </div>
                            <div class="grid grid-cols-7">
                                <div class="p-2 flex justify-center content-center"></div>
                                <div class="none col-span-3 text-center">:00</div>
                                <div class="col-span-3 text-center">:30</div>
                            </div>
                            <div class="grid grid-cols-7 border">
                                <template v-for="(hour, hourIndex) in hours" :key="hourIndex">
                                    <div class="p-2 border-b  flex justify-center content-center items-center">
                                        @{{ hour }}
                                    </div>
                                    <template v-for="(minute, minuteIndex) in minutes" :key="minuteIndex">
                                        <div :class="  ['border-b flex my-auto h-13.5', minuteIndex === 0 ? 'border-r' : '', timeSlots[hourIndex * 2 + minuteIndex].merged ? 'merged' : '']"
                                            :colspan="timeSlots[hourIndex * 2 + minuteIndex].colspan"
                                            class="col-span-3">
                                            <textarea  v-model="timeSlots[hourIndex * 2 + minuteIndex].task"
                                                type="text"  class="w-full text-sm p-1 resize-none h-full my-auto focus:outline-none overflow-hidden"></textarea>
                                            {{-- <input> --}}
                                            {{-- <button @click="toggleMerge(hourIndex * 2 + minuteIndex)"
                                                class="text-xs text-blue-500 mt-1">
                                                @{{ timeSlots[hourIndex * 2 + minuteIndex].merged ? 'Unmerge' : 'Merge' }}
                                            </button>
                                            <button @click="toggleBreak(hourIndex * 2 + minuteIndex)"
                                                class="text-xs text-red-500 mt-1">
                                                @{{ timeSlots[hourIndex * 2 + minuteIndex].isBreak ? 'Unmark Break' : 'Mark Break' }}
                                            </button> --}}
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp, ref, computed, watch, onMounted } = Vue;

        createApp({
            setup() {
                // Data
                const date = ref('');
                const priorities = ref(['', '', '']);
                const brainDump = ref('');
                const hours = ref([5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]);
                const minutes = ref([':00', ':30']);
                const timeSlots = ref(Array.from({ length: hours.value.length * 2 }, (_, index) => ({
                    task: '',
                    merged: false,
                    colspan: 1,
                    isBreak: false
                })));

                // Methods
                const toggleMerge = (index) => {
                    const slot = timeSlots.value[index];
                    slot.merged = !slot.merged;
                    slot.colspan = slot.merged ? 2 : 1;
                    if (slot.merged && index < timeSlots.value.length - 1) {
                        timeSlots.value[index + 1].merged = true;
                        timeSlots.value[index + 1].colspan = 0;
                    } else if (!slot.merged && index < timeSlots.value.length - 1) {
                        timeSlots.value[index + 1].merged = false;
                        timeSlots.value[index + 1].colspan = 1;
                    }
                };

                const toggleBreak = (index) => {
                    const slot = timeSlots.value[index];
                    slot.isBreak = !slot.isBreak;
                    if (slot.isBreak) {
                        slot.task = 'Break';
                    } else {
                        slot.task = '';
                    }
                };

                // Format date as YYYY-MM-DD
                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                // Set the current date
                const setCurrentDate = () => {
                    const today = new Date();
                    date.value = formatDate(today);
                };

                // Save to Local Storage
                const saveToLocalStorage = () => {
                    const data = {
                        date: date.value,
                        priorities: priorities.value,
                        brainDump: brainDump.value,
                        timeSlots: timeSlots.value
                    };
                    localStorage.setItem('timeboxData', JSON.stringify(data));
                };

                // Load from Local Storage
                const loadFromLocalStorage = () => {
                    // alert("Date.date: " + data.date)
                    // alert("Date.value: " + data.value)
                    const storedData = localStorage.getItem('timeboxData');
                    if (storedData) {
                        const data = JSON.parse(storedData);
                        if (data.date !== date.value) {
                            // If the date has changed, clear local storage
                            localStorage.removeItem('timeboxData');
                            return;
                        }
                        priorities.value = data.priorities;
                        brainDump.value = data.brainDump;
                        timeSlots.value = data.timeSlots;
                    }
                };

                // Watch for changes and save to local storage
                watch([date, priorities, brainDump, timeSlots], () => {
                    saveToLocalStorage();
                }, { deep: true });

                // On mount, set the current date and load saved data
                onMounted(() => {
                    setCurrentDate();
                    loadFromLocalStorage();
                });

                return {
                    date,
                    priorities,
                    brainDump,
                    hours,
                    minutes,
                    timeSlots,
                    toggleMerge,
                    toggleBreak
                };
            }
        }).mount('#app');
    </script>
</body>

</html>