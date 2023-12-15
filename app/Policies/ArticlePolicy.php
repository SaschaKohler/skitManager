<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArticlePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
<<<<<<< HEAD
        return ($user->role_id == 1 || $user->role_id == 2);
=======
//        return ($user->role_id == 1 || $user->role_id == 2);
        return auth()->user()->name1 == 'Sascha Kohler';
>>>>>>> origin/master

    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Article $article)
    {
<<<<<<< HEAD
        //
        return ($user->role_id == 1 || $user->role_id == 2);
=======
        return auth()->user()->name1 == 'Sascha Kohler';

//        return ($user->role_id == 1 || $user->role_id == 2);
>>>>>>> origin/master

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
<<<<<<< HEAD
        return ($user->role_id == 1 || $user->role_id == 2);
=======
        return auth()->user()->name1 == 'Sascha Kohler';
//        return ($user->role_id == 1 || $user->role_id == 2);
>>>>>>> origin/master

    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Article $article)
    {
        //
<<<<<<< HEAD
        return ($user->role_id == 1 || $user->role_id == 2);
=======
//        return ($user->role_id == 1 || $user->role_id == 2);
        return auth()->user()->name1 == 'Sascha Kohler';
>>>>>>> origin/master

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Article $article)
    {
        //
<<<<<<< HEAD
        return ($user->role_id == 1 || $user->role_id == 2);
=======
//        return ($user->role_id == 1 || $user->role_id == 2);
        return auth()->user()->name1 == 'Sascha Kohler';
>>>>>>> origin/master

    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Article $article)
    {
        //
<<<<<<< HEAD
        return ($user->role_id == 1 || $user->role_id == 2);
=======
//        return ($user->role_id == 1 || $user->role_id == 2);
        return auth()->user()->name1 == 'Sascha Kohler';
>>>>>>> origin/master

    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Article $article)
    {
        //
<<<<<<< HEAD
        return ($user->role_id == 1 || $user->role_id == 2);
=======
//        return ($user->role_id == 1 || $user->role_id == 2);
        return auth()->user()->name1 == 'Sascha Kohler';
>>>>>>> origin/master

    }
}
