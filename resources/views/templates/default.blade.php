@php
	/**
	 * @var Illuminate\Support\Collection|Yard\Data\PostData $postDataCollection
	 * @var Yard\QueryBlock\Block\BlockAttributes $attributes
	 */
@endphp

<div>
	@if ($postDataCollection->isNotEmpty())
		<div class="row">
			@foreach ($postDataCollection as $postData)
				@switch($postData->postType())
					@case('foo')
						{{-- bar --}}
					@break

					@default
						<div class="col-md-6 col-lg-4 mb-lg-4 mb-3">
							@include('partials.card.card-default', [
								'cardClass' => 'has-gradient-border',
								'displayFeaturedImage' => $attributes->displayImage(),
								'displayDate' => $attributes->displayDate(),
								'displayExcerpt' => $attributes->displayExcerpt(),
								'thumbnailUrl' => $postData->thumbnail()?->url(),
								'dateTime' => $postData->date(),
								'dateString' => $postData->date('j F Y'),
								'title' => $postData->title(),
								'link' => $postData->url(),
								'excerpt' => $postData->excerpt(),
							])
						</div>
					@break
				@endswitch
			@endforeach
		</div>
	@endif
</div>
