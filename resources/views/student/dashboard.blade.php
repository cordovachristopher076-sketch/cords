<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Student Portal & Attendance Check-In') }}
                </h2>
                <p class="text-sm text-gray-500">Welcome to Laboratory Attendance System</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                Student Account
            </span>
        </div>
    </x-slot>

    <!-- HTML5-QRCode Library para sa Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ showCheckInModal: false, html5QrcodeScanner: null }">
        
        <!-- ALERT MESSAGE AFTER CHECK-IN -->
        @if (session('status'))
            <div class="p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        <!-- 1. STUDENT QUICK ACTIONS / CHECK-IN CARD -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Current Session Status</h3>
                <p class="text-sm text-gray-500">You are currently logged in. Scan the station QR code to check in.</p>
            </div>

            <!-- BUTTON TO TRIGGER SCANNER MODAL -->
            <button type="button" 
                    @click="showCheckInModal = true; $nextTick(() => { startScanner(); })" 
                    style="background-color: #059669; color: #ffffff;" 
                    class="px-6 py-3 font-semibold rounded-lg shadow-sm transition flex items-center gap-2 hover:opacity-90 cursor-pointer">
                <svg class="w-5 h-5" style="color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span style="color: #ffffff;">Scan QR / Check-In Terminal</span>
            </button>
        </div>

        <!-- CHECK-IN / QR SCANNER MODAL POPUP -->
        <div x-show="showCheckInModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
            <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Scan Station QR Code</h3>
                    <button type="button" @click="stopScanner(); showCheckInModal = false;" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
                </div>
                
                <!-- QR CAMERA CONTAINER -->
                <div id="reader" class="w-full bg-gray-50 rounded-lg overflow-hidden border border-gray-300 mb-4"></div>

                <form id="checkinForm" action="{{ route('student.checkin') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                    <input type="hidden" name="student_id" value="STD-{{ auth()->id() }}">
                    <input type="hidden" name="course" value="BSIT">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detected Station Code</label>
                        <input type="text" id="stationInput" name="station" required placeholder="Scan QR or type e.g. PC-01" class="w-full border border-gray-300 p-2 rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="stopScanner(); showCheckInModal = false;" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" style="background-color: #059669; color: #ffffff;" class="px-4 py-2 rounded-md font-semibold hover:opacity-90">Confirm Check-In</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. RECENT STUDENT ATTENDANCE LOGS TABLE -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">My Attendance Logs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase border-b border-gray-200">
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Laboratory / Terminal</th>
                            <th class="px-6 py-3">Time In</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-hover bg-white">
                            <td class="px-6 py-4 font-semibold text-gray-800">Today</td>
                            <td class="px-6 py-4 text-gray-600">PC-01 (Lab 1)</td>
                            <td class="px-6 py-4 text-gray-500">08:30 AM</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">Present</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- SCRIPT PARA PA-ANDARIN ANG QR CAMERA -->
    <script>
        let html5QrcodeScanner = null;

        function startScanner() {
            if (!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 } 
                });

                html5QrcodeScanner.render((decodedText, decodedResult) => {
                    // Kapag na-scan ang QR code, ilalagay ang text sa input box
                    document.getElementById('stationInput').value = decodedText;
                    
                    // Stop scanner at auto submit
                    html5QrcodeScanner.clear();
                    document.getElementById('checkinForm').submit();
                }, (errorMessage) => {
                    // scanning error, ignore lang
                });
            }
        }

        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
            }
        }
    </script>
</x-app-layout>