from datetime import date

today = date.today()
current_date = today.strftime("%b-%d-%Y")

def get_date_last_modified(dirtry):
    import os.path, time
    print ("last modified: %s" % time.ctime(os.path.getmtime(dirtry)))
    print ("created: %s" % time.ctime(os.path.getctime(dirtry)))

def fetch_dir_contents(path):
    # import OS module
    import os
    
    # Get the list of all files and directories
    file_names = os.listdir(path)
    
    files_path = []
    for item in file_names:
        files_path.append(f"{path}/{item}")
    
    # return all files
    return [files_path,file_names]

    
def git_pusher(file_list):
    import github
    from github import Github
    from github import InputGitTreeElement
    
    # user = "umarfarouk98"
    # password = "*********"
    # g = Github(user,password)

    #---credentials & commit message-----#
    token = "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"
    g = github.Github(token)
    repo = g.get_user().get_repo('Dc-mongo-endpoints') # repo name
    commit_message = f'Server mongo_backup_{current_date}'

    #------create new branch------#
    source_branch = 'master'
    target_branch = f'mongo_backup_{current_date}' #new branch
    sb = repo.get_branch(source_branch)
    try:
        repo.create_git_ref(ref='refs/heads/' + target_branch, sha=sb.commit.sha)
    except Exception as e:
        pass
    
    
    #----end create new branch------#

    #-----initialize new branch-------#
    try:
        branch_ref = repo.get_git_ref(f'heads/{target_branch}')
        branch_sha = branch_ref.object.sha
        branch_tree = repo.get_git_tree(branch_sha)

    except Exception as e:
        print(e)
    
    # print(branch_ref)
    # print(branch_sha)
    # print(branch_tree)
    #-------end init new branch--------#
    
    #----loop through files, open them nd create as git elements-----#
    element_list = list() #--list to append gittree element
    for i in file_list:
        try:
            with open(i, 'r') as input_file:
                data = input_file.read()
            element = InputGitTreeElement(i, '100644', 'blob', data)
            element_list.append(element)
        except Exception as e:
            print(e)
    
    # print(element_list)
    #-------end looping through elements--------#

    
    #-------try and excep to create and make commit------#
    try:
        tree = repo.create_git_tree(element_list, branch_tree)
        parent = repo.get_git_commit(branch_sha)
        commit = repo.create_git_commit(commit_message, tree, [parent])
    
    except Exception as e:
        print("error", e)
    
    # print(tree)
    # print(parent)
    # print(commit)
    #---------end try nd xcpt for commit------------#
    
    #-------create result depending on response-------#
    try:
        branch_ref.edit(commit.sha)
        result = "commit successfull...."
    except Exception as e:
        result = f" {e}, ecommit failed !!!"
    
    print(result)
    #----------end create nd print result-------------#


if __name__ == "__main__":
    # print(fetch_dir_contents("../mongo/api")[0])
    # get_date_last_modified('mongo/api')
    all_dirs = ['mongo', 'scripts' ]
    for i in all_dirs:
        git_pusher(fetch_dir_contents(i)[0])
