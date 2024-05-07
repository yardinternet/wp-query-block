@php
	/**
	 * Template: Standaard
	 *
	 * @var Illuminate\Support\Collection|Yard\Data\PostData $postDataCollection
	 * @var Yard\QueryBlock\Block\BlockAttributes $attributes
	 */
@endphp


@forelse ($postDataCollection as $postData)
	<div>
		<h3>{{ $postData->title() }}</h3>

		@if ($attributes->displayDate())
			<time datetime="{{ $postData->date }}">{{ $postData->date() }}</time>
		@endif

		@if ($attributes->displayImage() && $postData->hasThumbnail())
			<img src="{{ $postData->thumbnail()?->url('thumbnail') }}" alt="{{ $postData->thumbnail()?->alt() }}">
		@endif

		@if ($attributes->displayExcerpt() && $postData->excerpt())
			<p>{!! $postData->excerpt() !!}</p>
		@endif
	</div>
@empty
	<p>Geen resultaten gevonden.</p>
@endforelse
