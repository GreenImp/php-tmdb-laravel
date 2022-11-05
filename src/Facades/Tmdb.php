<?php

namespace Tmdb\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Tmdb\Api\Account getAccountApi()
 * @method static \Tmdb\Api\Authentication getAuthenticationApi()
 * @method static \Tmdb\Api\Certifications getCertificationsApi()
 * @method static \Tmdb\Api\Changes getChangesApi()
 * @method static \Tmdb\Api\Collections getCollectionsApi()
 * @method static \Tmdb\Api\Companies getCompaniesApi()
 * @method static \Tmdb\Api\Configuration getConfigurationApi()
 * @method static \Tmdb\Api\Credits getCreditsApi()
 * @method static \Tmdb\Api\Discover getDiscoverApi()
 * @method static \Tmdb\Api\Find getFindApi()
 * @method static \Tmdb\Api\Genres getGenresApi()
 * @method static \Tmdb\Api\GuestSession getGuestSessionApi()
 * @method static \Tmdb\Api\Jobs getJobsApi()
 * @method static \Tmdb\Api\Keywords getKeywordsApi()
 * @method static \Tmdb\Api\Lists getListsApi()
 * @method static \Tmdb\Api\Movies getMoviesApi()
 * @method static \Tmdb\Api\Networks getNetworksApi()
 * @method static \Tmdb\Api\People getPeopleApi()
 * @method static \Tmdb\Api\Reviews getReviewsApi()
 * @method static \Tmdb\Api\Search getSearchApi()
 * @method static \Tmdb\Api\Timezones getTimezonesApi()
 * @method static \Tmdb\Api\Tv getTvApi()
 * @method static \Tmdb\Api\TvSeason getTvSeasonApi()
 * @method static \Tmdb\Api\TvEpisode getTvEpisodeApi()
 * @method static \Tmdb\Api\TvEpisodeGroup getTvEpisodeGroupApi()
 *
 * @see \Tmdb\Client
 */
class Tmdb extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'Tmdb\Client';
    }
}
