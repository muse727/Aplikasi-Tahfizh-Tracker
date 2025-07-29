@props(['leaderboard'])

<ul role="list" class="divide-y divide-gray-200">
    @if($leaderboard->isNotEmpty() && $leaderboard->first()->progress_records_sum_progress_count > 0)
        @foreach ($leaderboard as $rank => $student)
            <li class="py-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0"><span class="text-xl font-bold text-gray-400 w-6 text-center">{{ $rank + 1 }}</span></div>
                    <div class="flex-shrink-0"><img class="w-10 h-10 rounded-full" src="https://api.dicebear.com/8.x/initials/svg?seed={{ urlencode($student->name) }}" alt="avatar"></div>
                    <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">{{ $student->name }}</p></div>
                    <div class="inline-flex items-center text-sm font-semibold text-gray-900">{{ $student->progress_records_sum_progress_count ?? 0 }} Poin</div>
                </div>
            </li>
        @endforeach
    @else
        <li class="py-4 text-center text-gray-500">Belum ada data peringkat untuk kelas ini.</li>
    @endif
</ul>