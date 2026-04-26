<?php

namespace App\Providers;

use App\Interfaces\AboutRepositoryInterface;
use App\Interfaces\AssistantTeacherRepositoryInterface;
use App\Interfaces\CourseDetailRepositoryInterface;
use App\Interfaces\CourseRepositoryInterface;
use App\Interfaces\FeatureRepositoryInterface;
use App\Interfaces\FooterRepositoryInterface;
use App\Interfaces\HomeRepositoryInterface;
use App\Interfaces\StageRepositoryInterface;
use App\Interfaces\StudentRepositoryInterface;
use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\TeacherRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\AboutRepository;
use App\Repositories\AssistantTeacherRepository;
use App\Repositories\CourseDetailRepository;
use App\Repositories\CourseRepository;
use App\Repositories\FeatureRepository;
use App\Repositories\FooterRepository;
use App\Repositories\HomeRepository;
use App\Repositories\StageRepository;
use App\Repositories\StudentRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\UserRepository;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(StageRepositoryInterface::class, StageRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->bind(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(AssistantTeacherRepositoryInterface::class, AssistantTeacherRepository::class);
        $this->app->bind(HomeRepositoryInterface::class, HomeRepository::class);
        $this->app->bind(FeatureRepositoryInterface::class, FeatureRepository::class);
        $this->app->bind(AboutRepositoryInterface::class, AboutRepository::class);
        $this->app->bind(FooterRepositoryInterface::class, FooterRepository::class);
        $this->app->bind(CourseDetailRepositoryInterface::class, CourseDetailRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}


